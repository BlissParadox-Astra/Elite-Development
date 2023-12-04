<template>
    <v-container class="mt-12 " >
        <v-row justify="center">
            <v-col cols="12">
                <v-row>
                    <v-col cols="12" md="4" lg="4" sm="12">
                        <v-text-field class="ml-1" label="Transaction ID" variant="plain" v-model="transaction_number"
                            readonly />
                    </v-col>

                    <v-col cols="12" md="2" lg="3" sm="12" class="d-flex align-center">
                        <v-btn class="text clickable-text" color="#23b78d" block @click="generateAndFetchInvoiceNumber"
                            :disabled="isGeneratingInvoiceNumber">[GENERATE]</v-btn>
                    </v-col>

                    <v-col cols="12" md="1" lg="2" sm="12">
                        <v-text-field label="Transaction Date" type="date" variant="plain" v-model="transaction_date" />
                    </v-col>

                    <v-col cols="12" md="4" lg="4" sm="12">
                        <SearchField ref="barcodeSearchField" @searchBarcode="handleBarcodeScan" :searchLabel="searchLabel"
                            :searchType="'barcode'" :disabled="!isTransactionNumberPresent" />
                    </v-col>

                    <v-col cols="12" md="2" lg="3" sm="12" class="d-flex align-center">
                        <v-btn class="text clickable-text" color="#23b78d" block @click="showBrowseProductForm"
                            :disabled="!canBrowseProduct">[CLICK HERE TO BROWSE PRODUCT] </v-btn>
                    </v-col>

                    <v-col cols="12" md="5" sm="12" lg="3" class="d-flex align-center ml-15">
                        <v-card class="pa-3 total-card" style="height: 50px; width: 80%;">
                            <v-row class="text-left" style="height: 80%;">
                                <v-col cols="12">
                                    <span class="total-label" style="margin-right: 8px;">Overall Total:</span>
                                    <span class="total-value">{{ calculateOverallTotal() }}</span>
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" :transaction_date="transaction_date"
                    :transaction_number="transaction_number" :transact_by="transact_by" item-value="id" class="elevation-1"
                    hide-default-footer fixed-header height="350">
                    <template v-slot:custom-sort="{ header }">
                        <span v-if="header.key === 'actions'">Actions</span>
                    </template>
                    <template v-slot:item="{ item, index }">
                        <tr>
                            <td>{{ displayedIndex + index }}</td>
                            <td>{{ item.product_code }}</td>
                            <td>{{ item.barcode }}</td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.price }}</td>
                            <td>
                                <span v-if="isTransactionPage">
                                    <span @click="openEditQuantityDialog(item)">{{ item.quantity }}</span>
                                </span>
                                <span v-else>{{ item.quantity }}</span>
                            </td>
                            <td>{{ item.total }}</td>
                            <td>
                                <span>
                                    <v-icon @click="subtractProduct(item)">mdi-minus-circle</v-icon>
                                </span>
                                <span style="margin-left: 2px;">
                                    <v-icon @click="addProduct(item)">mdi-plus-circle</v-icon>
                                </span>
                                <span style="margin-left: 2px;">
                                    <v-icon @click="showDeleteConfirmation(item)" color="error">mdi-delete</v-icon>
                                </span>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:bottom>
                        <div class="text-center pt-8 pagination">
                            <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                                :disabled="currentPage === 1">Previous</v-btn>

                            <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                                :class="{ active: pageNumber === currentPage }" class="pagination-button">
                                {{ pageNumber }}
                            </v-btn>

                            <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                                :disabled="currentPage === totalPages">Next</v-btn>
                        </div>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
        <!--Browse Product -->
        <v-row v-if="showBrowseProduct">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="8" md="8" lg="8" xl="10" class="form-container">
                        <BrowseProduct @close="closeBrowseProductForm" :addToCart="addToCartProduct"
                            :context="'transaction'" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <!-- Edit Quantity Dialog -->
        <v-dialog v-model="showEditQuantityDialog" max-width="400" class="center-dialog  no-background">
            <v-card>
                <v-card-title>Edit Quantity</v-card-title>
                <v-card-text>
                    <v-text-field v-model="editedQuantity" label="New Quantity" @keypress="filterNumeric"></v-text-field>
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <v-btn color="primary" @click="saveEditedQuantity"
                        :disabled="isEditQuantitySaveButtonDisabled">Update</v-btn>
                    <v-btn @click="closeEditQuantityDialog">Cancel</v-btn>
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
        <v-row class="d-flex justify-space-between mt-n4">
            <v-col cols="12" sm="6" lg="3" class="text-start">
                <v-btn to="/cashier-dashboard" color="#23b78d" block>BACK</v-btn>
            </v-col>
            <v-col cols="12" sm="6" lg="3" class="text-end">
                <v-btn color="#23b78d" @click="showConfirmation" style="width: 150px;"
                    :disabled="isSoldButtonDisabled" block>SOLD</v-btn>
            </v-col>
            <v-dialog v-model="showConfirmationDialog" max-width="400" class="center-dialog  no-background">
                <v-card>
                    <v-card-title class="bg-teal pa-1 text-center">
                        Confirm Transaction
                    </v-card-title>
                    <v-card-text class="text-center">
                        ARE YOU SURE YOU WANT TO COMPLETE THIS TRANSACTION?
                    </v-card-text>
                    <v-card-actions class="d-flex justify-center">
                        <div>
                            <v-btn color="#23b78d" @click="saveRecord" style="width: 150px;">Yes</v-btn>
                            <v-btn color="#068863" @click="cancelSave">No</v-btn>
                        </div>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </v-container>
</template>
<script>
import BrowseProduct from '../../commons/BrowseProduct.vue';
import SearchField from '../../commons/SearchField.vue';
import debounce from 'lodash/debounce';
import { mapState } from 'vuex';
import axios from 'axios';
export default {
    name: 'TransactionCart',
    components: {
        BrowseProduct,
        SearchField,
    },
    data() {
        return {
            itemsPerPage: 10,
            currentPage: 1,
            totalItems: 0,
            showEditQuantityDialog: false,
            showConfirmationDialog: false,
            showBrowseProduct: false,
            transaction_date: '',
            transaction_number: '',
            isGeneratingInvoiceNumber: false,
            editedQuantity: 0,
            editingIndex: -1,
            snackbar: false,
            isTransactionPage: true,
            searchLabel: 'Search Barcode Here',
            snackbarColor: '',
            scannedData: '',
            loading: false,
            products: [],
            headers: [
                { title: '#', value: 'index' },
                { title: "Product Code", key: "product_code" },
                { title: "Barcode", key: "barcode" },
                { title: "Description", key: "description" },
                { title: "Price", key: "price" },
                { title: "Quantity", key: "quantity" },
                { title: "Total", key: "total" },
                { title: 'Actions', key: 'actions', sortable: false }
            ],
        };
    },

    computed: {
        ...mapState({
            user: state => state.user,
        }),

        transact_by() {
            if (this.user && this.user.first_name && this.user.last_name) {
                return `${this.user.first_name} ${this.user.last_name}`;
            } else {
                return '';
            }
        },

        isEditQuantitySaveButtonDisabled() {
            return this.editedQuantity === '' || isNaN(this.editedQuantity);
        },

        canBrowseProduct() {
            return this.transaction_number && this.transaction_date;
        },

        isSoldButtonDisabled() {
            return this.transaction_number === '' || this.transaction_date === '' || this.products.length === 0;
        },

        isTransactionNumberPresent() {
            return !!this.transaction_number;
        },

        displayedIndex() {
            return (this.currentPage - 1) * this.itemsPerPage + 1;
        },

        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },

        visiblePageRange() {
            const maxVisiblePages = 5;
            const halfMaxVisiblePages = Math.floor(maxVisiblePages / 2);
            const firstPage = Math.max(1, this.currentPage - halfMaxVisiblePages);
            const lastPage = Math.min(this.totalPages, firstPage + maxVisiblePages - 1);

            const range = [];
            for (let i = firstPage; i <= lastPage; i++) {
                range.push(i);
            }

            return range;
        },
    },

    created() {
        const queryDate = this.$route.query.date;
        if (queryDate) {
            this.transaction_date = queryDate;
        } else {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth() + 1;
            const day = today.getDate();
            this.transaction_date = `${year}-${month}-${day}`;
        }
        this.totalItems = this.products.length;
        this.generateAndFetchInvoiceNumber();
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyDown);
    },

    beforeUnmount() {
        window.removeEventListener('keydown', this.handleKeyDown);
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

        filterNumeric(event) {
            const keyCode = event.keyCode || event.which;
            const key = String.fromCharCode(keyCode);

            if (/^\d*\.?\d*$/.test(key)) {
                return;
            }

            event.preventDefault();
        },

        generateAndFetchInvoiceNumber() {
            this.isGeneratingInvoiceNumber = true;
            axios.get('/transaction/generate-transaction-number')
                .then(response => {
                    this.transaction_number = response.data.transaction_number;
                })
                .catch(error => {
                    console.error('Error fetching invoice number', error);
                });
        },

        addToCartProduct(product) {
            const existingProduct = this.products.find(p => p.product_code === product.product_code);

            if (existingProduct) {
                const totalQuantity = existingProduct.quantity + 1;

                if (totalQuantity > product.stock_on_hand) {
                    this.showSnackbar('Not enough stock available for this product', 'error');
                    return;
                }

                existingProduct.quantity++;
                existingProduct.total = this.calculateTotal(existingProduct);
            } else {
                if (1 > product.stock_on_hand) {
                    this.showSnackbar('Not enough stock available for this product', 'error');
                    return;
                }

                const newProduct = {
                    id: product.id,
                    product_code: product.product_code,
                    barcode: product.barcode,
                    description: product.description,
                    price: product.price,
                    quantity: 1,
                    total: product.price * 1,
                };

                this.products.push(newProduct);
                this.loading.false;
            }
            this.totalItems = this.products.length;
            return true;
        },

        subtractProduct(item) {
            if (item.quantity > 1) {
                item.quantity--;
                item.total = this.calculateTotal(item);
            }
        },

        addProduct(item) {
            axios.get(`transaction/get-products/${item.id}`)
                .then(response => {
                    const inventoryItem = response.data.product;

                    if (inventoryItem && item.quantity + 1 > inventoryItem.stock_on_hand) {
                        this.showSnackbar('Not enough stock available for this product', 'error');
                        return;
                    }

                    item.quantity++;
                    item.total = this.calculateTotal(item);
                })
                .catch(error => {
                    console.error('Error fetching inventory data for adding product', error);
                });
        },

        calculateOverallTotal() {
            return this.products.reduce((total, product) => total + parseFloat(product.total), 0);
        },

        calculateTotal(product) {
            const quantity = parseFloat(product.quantity);
            const price = parseFloat(product.price);

            if (!isNaN(quantity) && !isNaN(price)) {
                return quantity * price;
            } else {
                return 0;
            }
        },

        openEditQuantityDialog(item) {
            this.editingIndex = this.products.indexOf(item);
            this.editedQuantity = item.quantity;
            this.showEditQuantityDialog = true;
        },

        saveEditedQuantity() {
            if (this.editingIndex !== -1) {
                const editedProduct = this.products[this.editingIndex];

                axios.get(`transaction/get-products/${editedProduct.id}`)
                    .then(response => {
                        const inventoryItem = response.data.product;

                        if (inventoryItem && this.editedQuantity > inventoryItem.stock_on_hand) {
                            this.snackbarColor = 'error';
                            this.showSnackbar('Not enough stock available for this product', 'error');
                            return;
                        }

                        editedProduct.quantity = this.editedQuantity;
                        editedProduct.total = this.calculateTotal(editedProduct);

                        this.showEditQuantityDialog = false;
                        this.editingIndex = -1;
                        this.editedQuantity = 0;
                        this.snackbarColor = 'success';
                        this.showSnackbar('Quantity updated successfully', 'success');
                    })
                    .catch(error => {
                        console.error('Error fetching inventory data for editing quantity', error);
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
            const transactionRequests = this.products.map((product) => {
                return {
                    transaction_number: this.transaction_number,
                    transaction_date: this.transaction_date,
                    product_id: product.id,
                    quantity: product.quantity,
                };
            });

            const transactionData = {
                transaction_requests: transactionRequests,
            };

            axios
                .post("/transaction", transactionData)
                .then(() => {
                    this.resetData();
                    this.isGeneratingInvoiceNumber = false;
                    this.totalItems = this.products.length;
                    this.generateAndFetchInvoiceNumber();
                    this.snackbarColor = 'success';
                    this.showSnackbar('Transacted Successfully', 'success');
                })
                .catch((error) => {
                    console.error("Transaction errored", error);
                    this.isGeneratingInvoiceNumber = true;
                    this.snackbarColor = 'error';
                    this.showSnackbar('Transaction Failed. Please try again later.', 'error');
                });
        },

        resetData() {
            this.products = [];
            this.transaction_number = '';
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
}
</script>

<style scoped>
.mt-12 {
    overflow-x: hidden;
  /* white-space: nowrap; */
}
.total-card {

    background-color: #d8d3d3;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
    height: 30%;

}

.total-value {
    font-weight: bold;
    color: #333;
}

.form-container {
    position: absolute;
    top: 0;
    left: 1;
    right: 1;
    z-index: 999;

}

.center-dialog {
    position: fixed;
    left: 30%;
    right: 30%;
    /* transform: translate(-50%, -50%); */
}

.clickable-text {
    cursor: pointer;
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
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}
</style>