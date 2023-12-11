<template>
    <v-main class="section2 mt-14">
        <v-container>
            <v-row class="pa-2">
                <v-col cols="12" sm="5" md="5" lg="4" xl="5">
                    <v-text-field label="Reference Number" v-model="reference_number" readonly />
                </v-col>

                <v-col cols="12" sm="3" class="d-flex justify-center align-center mt-n5">
                    <v-btn color="#23b78d" block @click="generateAndFetchReferenceNumber"
                        :disabled="isGeneratingReferenceNumber">Generate</v-btn>
                </v-col>

                <v-col cols="12" sm="4" md="3" lg="4" xl="5">
                    <v-text-field label="Stock In Date" type="text" variant="plain" v-model="stock_in_date" readonly />
                </v-col>

                <v-col cols="12" sm="5" md="5" lg="4" xl="2" class="mb-6">
                    <SearchField ref="barcodeSearchField" @searchBarcode="handleBarcodeScan" :searchLabel="searchLabel"
                        :searchType="'barcode'" :disabled="!isReferenceNumberPresent" />
                </v-col>

                <v-col cols="12" sm="3" class="d-flex justify-center align-center mt-n6">
                    <v-btn color="#23b78d" block @click="showBrowseProductForm" :disabled="!canBrowseProduct">Browse
                        Product</v-btn>
                </v-col>

                <v-col cols="12" sm="4" md="3" lg="4" xl="5" class="d-flex justify-center align-center mt-n3">
                    <v-text-field label="Stock In By" readonly :model-value="stock_in_by" />
                </v-col>
            </v-row>
            <v-row v-if="showBrowseProduct">
                <v-col cols="12">
                    <v-row class="d-flex justify-center">
                        <v-col cols="12" sm="10" md="10" lg="10" xl="10" class="form-container">
                            <BrowseProduct @close="closeBrowseProductForm" :addToCart="addToCartProduct"
                                :context="'stockIn'" />
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
                :items-per-page="itemsPerPage" density="compact" :reference_number="reference_number"
                :stock_in_date="stock_in_date" :stock_in_by="stock_in_by" item-value="id" class="elevation-1"
                hide-default-footer fixed-header height="310">
                <template v-slot:custom-sort="{ header }">
                    <span v-if="header.key === 'actions'">Actions</span>
                </template>
                <template v-slot:item="{ item, index }">
                    <tr>
                        <td>{{ displayedIndex + index }}</td>
                        <td>{{ item.reference_number }}</td>
                        <td>{{ item.product_code }}</td>
                        <td>{{ item.barcode }}</td>
                        <td>{{ item.description }}</td>
                        <td>
                            <span v-if="isStockEntryPage">
                                <span @click="openEditQuantityDialog(item)" class="icon-pointer">{{ item.quantity_added
                                }}</span>
                            </span>
                            <span v-else>{{ item.quantity_added }}</span>
                        </td>
                        <td>{{ item.stock_in_date }}</td>
                        <td>{{ item.stock_in_by }}</td>
                        <td>
                            <span>
                                <v-icon @click="subtractProduct(item)" size="22" class="ml-n3"
                                    color="#23b78d">mdi-minus-circle</v-icon>
                            </span>
                            <span>
                                <v-icon @click="addProduct(item)" size="22" color="#23b78d">mdi-plus-circle</v-icon>
                            </span>
                            <span>
                                <v-icon @click="showDeleteConfirmation(item)" color="#23b78d" size="22">mdi-delete</v-icon>
                            </span>
                        </td>
                    </tr>
                </template>
                <template v-slot:bottom>
                    <v-col cols="12">
                        <div v-if="totalPages > 1" class="text-center pt-5 pagination">
                            <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                                :disabled="currentPage === 1">
                                <v-icon>mdi-chevron-left</v-icon> Prev
                            </v-btn>

                            <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                                :class="{ active: pageNumber === currentPage }" class="pagination-button">
                                {{ pageNumber }}
                            </v-btn>

                            <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                                :disabled="currentPage === totalPages">
                                Next <v-icon>mdi-chevron-right</v-icon>
                            </v-btn>
                        </div>
                    </v-col>
                </template>
            </v-data-table>
            <v-row class="mt-5 save-btn">
                <v-col cols="12" sm="12" md="2" class="text-start">
                    <v-btn color="#23b78d" @click="showConfirmation" :disabled="isSaveButtonDisabled" block>Save</v-btn>
                </v-col>
            </v-row>
            <v-dialog v-model="showConfirmationDialog" max-width="400" class="center-dialog  no-background">
                <v-card>
                    <v-card-title class="d-flex justify-center bg-teal pa-3">
                        Confirm Save
                    </v-card-title>
                    <v-card-text class="text-center">
                        ARE YOU SURE YOU WANT TO SAVE THESE STOCK-IN RECORD(S)?
                    </v-card-text>
                    <v-card-actions class="d-flex justify-center">
                        <div>
                            <v-btn color="#23b78d" @click="saveRecord" style="width: 150px;">Yes</v-btn>
                            <v-btn @click="cancelSave" color="#068863">No</v-btn>
                        </div>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="showEditQuantityDialog" max-width="400" class="center-dialog  no-background">
                <v-card>
                    <v-card-title class="text-center bg-teal pa-3">Edit Quantity</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="editedQuantity" label="New Quantity"
                            @keypress="filterNumeric"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="d-flex justify-center">
                        <v-btn color="#23b78d" @click="saveEditedQuantity"
                            :disabled="isEditQuantitySaveButtonDisabled">Update</v-btn>
                        <v-btn @click="closeEditQuantityDialog" color="#068863">Cancel</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-snackbar v-model="snackbar" right top :color="snackbarColor">
                {{ snackbarText }}
                <template v-slot:actions>
                    <v-btn color="pink" variant="text" @click="snackbar = false">
                        Close
                    </v-btn>
                </template>
            </v-snackbar>
        </v-container>
    </v-main>
</template>

<script>
import BrowseProduct from '../../commons/BrowseProduct.vue';
import SearchField from '../../commons/SearchField.vue';
import { VIcon } from "vuetify/lib/components";
import debounce from 'lodash/debounce';
import { mapState } from 'vuex';
import axios from 'axios';
export default {
    name: "StockEntry",
    components: {
        BrowseProduct,
        SearchField,
        VIcon,
    },
    data() {
        return {
            itemsPerPage: 10,
            currentPage: 1,
            id: 1,
            totalItems: 0,
            showEditQuantityDialog: false,
            showConfirmationDialog: false,
            showBrowseProduct: false,
            isGeneratingReferenceNumber: false,
            products: [],
            loading: false,
            editedQuantity: 0,
            editingIndex: -1,
            reference_number: '',
            stock_in_date: '',
            isStockEntryPage: true,
            searchLabel: 'Search Barcode Here',
            snackbar: false,
            snackbarColor: '',
            scannedData: '',
            headers: [
                { title: '#', value: 'index' },
                { title: 'Reference No.', key: 'reference_number' },
                { title: 'Product Code', key: 'product_code' },
                { title: 'Barcode', key: 'barcode' },
                { title: 'Description', key: 'description' },
                { title: 'Quantity', key: 'quantity_added' },
                { title: 'Stock In Date', key: 'stock_in_date' },
                { title: 'Stock In By', key: 'stock_in_by' },
                { title: 'Actions', key: 'actions', sortable: false }
            ],
        };
    },

    computed: {
        ...mapState({
            user: state => state.user,
        }),

        stock_in_by() {
            if (this.user && this.user.first_name && this.user.last_name) {
                return `${this.user.first_name} ${this.user.last_name}`;
            } else {
                return '';
            }
        },

        canBrowseProduct() {
            return this.reference_number && this.stock_in_date && this.stock_in_by;
        },

        isEditQuantitySaveButtonDisabled() {
            return this.editedQuantity <= 0 || isNaN(this.editedQuantity);
        },

        isSaveButtonDisabled() {
            return this.reference_number === '' || this.stock_in_date === '' || this.stock_in_by === '' || this.products.length === 0;
        },

        isReferenceNumberPresent() {
            return !!this.reference_number;
        },

        displayedIndex() {
            return (this.currentPage - 1) * this.itemsPerPage + 1;
        },

        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },

        visiblePageRange() {
            const smallScreenMaxPages = 2;
            const largeScreenMaxPages = 5;

            const maxVisiblePages = this.isSmallScreen ? smallScreenMaxPages : largeScreenMaxPages;
            const halfMaxVisiblePages = Math.floor(maxVisiblePages / 2);
            const firstPage = Math.max(1, this.currentPage - halfMaxVisiblePages);
            const lastPage = Math.min(this.totalPages, firstPage + maxVisiblePages - 1);

            const range = [];
            for (let i = firstPage; i <= lastPage; i++) {
                range.push(i);
            }

            return range;
        },

        isSmallScreen() {
            return window.innerWidth < 768;
        },
    },

    mounted() {
        this.updateStockInDate();

        this.intervalId = setInterval(() => {
            this.updateStockInDate();
        }, 1000);

        window.addEventListener('keydown', this.handleKeyDown);
        this.$refs.barcodeSearchField.$refs.searchField.focus();
    },

    beforeUnmount() {
        clearInterval(this.intervalId);

        window.removeEventListener('keydown', this.handleKeyDown);
    },

    created() {
        this.totalItems = this.products.length;
        this.generateAndFetchReferenceNumber();
    },

    methods: {
        handleKeyDown: debounce(function (event) {
            this.scannedData = '';
            if (event.key === 'Enter') {
                this.handleBarcodeScan(this.scannedData);
            } else {
                this.scannedData += event.key;
            }
        }, 300),

        handleBarcodeScan(data) {

            if (!data.trim()) {
                return;
            }

            this.$refs.barcodeSearchField.searchQuery = '';

            axios.get(`/product/by-barcode?barcode=${data}`)
                .then(response => {
                    const product = response.data.product;
                    if (product) {
                        this.addToCartProduct(product);
                    } else {
                        this.showSnackbar('Product not found', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching product by barcode', error);

                    if (error.response && error.response.data && error.response.data.error) {
                        this.showSnackbar(error.response.data.error, 'error');
                    } else {
                        this.showSnackbar('Error fetching product details', 'error');
                    }
                });

            this.scannedData = '';
        },

        updateStockInDate() {
            const today = new Date();
            const formattedDate = today.toLocaleString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            });

            this.stock_in_date = formattedDate;
        },

        filterNumeric(event) {
            const keyCode = event.keyCode || event.which;
            const key = String.fromCharCode(keyCode);

            if (/^\d*\.?\d*$/.test(key)) {
                return;
            }

            event.preventDefault();
        },

        generateAndFetchReferenceNumber() {
            this.isGeneratingReferenceNumber = true;
            axios.get('/stock-in/generate-reference-number')
                .then(response => {
                    this.reference_number = response.data.reference_number;
                })
                .catch(error => {
                    console.error('Error fetching reference number', error);
                });
        },

        addToCartProduct(product) {
            const existingProduct = this.products.find(p => p.product_code === product.product_code);
            if (existingProduct) {
                existingProduct.quantity_added++;
            } else {
                const newProduct = {
                    id: product.id,
                    product_code: product.product_code,
                    barcode: product.barcode,
                    description: product.description,
                    reference_number: this.reference_number,
                    quantity_added: 1,
                    stock_in_date: this.stock_in_date,
                    stock_in_by: this.stock_in_by,
                };
                this.products.push(newProduct);
                this.loading = false;
            }
            this.totalItems = this.products.length;
            return true;
        },

        subtractProduct(item) {
            if (item.quantity_added > 1) {
                item.quantity_added--;
            }
            this.$refs.barcodeSearchField.$refs.searchField.focus();
        },

        addProduct(item) {
            item.quantity_added++;
            this.$refs.barcodeSearchField.$refs.searchField.focus();
        },

        openEditQuantityDialog(item) {
            this.editingIndex = this.products.indexOf(item);
            this.editedQuantity = item.quantity_added;
            this.showEditQuantityDialog = true;
        },

        saveEditedQuantity() {
            if (this.editingIndex !== -1) {
                this.products[this.editingIndex].quantity_added = this.editedQuantity;
                this.showEditQuantityDialog = false;
                this.editingIndex = -1;
                this.editedQuantity = 0;
                this.snackbarColor = 'success';
                this.showSnackbar('Quantity updated successfully', 'success');
                this.$nextTick(() => {
                    this.$refs.barcodeSearchField.$refs.searchField.focus();
                });
            } else {
                console.error('Editing index is invalid');
                this.snackbarColor = 'error';
                this.showSnackbar('Failed to update quantity. Please try again later.', 'error');
            }
        },

        closeEditQuantityDialog() {
            this.showEditQuantityDialog = false;
            this.editingIndex = -1;
            this.editedQuantity = 0;
        },

        deleteProductRow(item) {
            const index = this.products.indexOf(item);
            if (index !== -1) {
                this.products.splice(index, 1);
                this.totalItems = this.products.length;
                this.snackbarColor = 'success';
                this.showSnackbar('Successfully removed product from cart');
                this.$refs.barcodeSearchField.$refs.searchField.focus();
            } else {
                this.snackbarColor = 'error';
                this.showSnackbar('Error removing product from cart');
            }
        },

        showDeleteConfirmation(item) {
            this.itemToDelete = item;
            this.deleteProductRow(item);
        },

        saveRecord() {
            this.showConfirmationDialog = false;
            const stockInRequests = this.products.map((product) => {
                return {
                    reference_number: this.reference_number,
                    stock_in_date: this.stock_in_date,
                    stock_in_by: this.stock_in_by,
                    product_id: product.id,
                    quantity_added: product.quantity_added,
                };
            });

            const stockInData = {
                stock_in_requests: stockInRequests,
            };

            axios
                .post("/stock-in", stockInData)
                .then(() => {
                    this.resetData();
                    this.isGeneratingReferenceNumber = false;
                    this.totalItems = this.products.length;
                    this.generateAndFetchReferenceNumber();
                    this.snackbarColor = 'success';
                    this.showSnackbar('Stock-In records saved successfully', 'success');
                    this.$refs.barcodeSearchField.$refs.searchField.focus();
                })
                .catch((error) => {
                    console.error("Error saving Stock-In records", error);
                    this.isGeneratingReferenceNumber = true;
                    this.snackbarColor = 'error';
                    this.showSnackbar('Failed to save Stock-In records. Please try again later.', 'error');
                });
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },

        gotoPage(pageNumber) {
            this.currentPage = pageNumber;
        },

        showConfirmation() {
            this.showConfirmationDialog = true;
        },

        cancelSave() {
            this.showConfirmationDialog = false;
        },

        showBrowseProductForm() {
            this.showBrowseProduct = true;
        },

        closeBrowseProductForm() {
            this.showBrowseProduct = false;
            this.$nextTick(() => {
                this.$refs.barcodeSearchField.$refs.searchField.focus();
            });
        },

        resetData() {
            this.products = [];
            this.reference_number = '';
        },

        showSnackbar(text, color, timeout = 3000) {
            this.snackbarText = text;
            this.snackbarColor = color;
            this.snackbar = true;

            setTimeout(() => {
                this.hideSnackbar();
            }, timeout);
        },

        hideSnackbar() {
            this.snackbar = false;
        },
    },
};
</script>
<style scoped>
.form-container {
    position: absolute;
    top: 0;
    left: 1;
    right: 1;
    z-index: 999;
}

.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-button {
    padding: 6px 12px;
    margin: 0 4px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
}

.pagination-button.active {
    background-color: #23b78d;
    color: #fff;
    border-color: #23b78d;
}

.icon-pointer {
    cursor: pointer;
    margin-right: 30px;
}
</style>
