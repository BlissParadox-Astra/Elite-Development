<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12">
                        <h2 class="text-center">TRANSACTION CART</h2>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" sm="4" lg="3">
                        <v-text-field class="ml-1" label="Transaction ID" variant="plain" v-model="transaction_number"
                            readonly />
                    </v-col>
                    <v-col cols="12" sm="6" lg="3" class="d-flex align-center">
                        <h5 class="text-blue clickable-text" @click="generateAndFetchInvoiceNumber"
                            :disabled="isGeneratingInvoiceNumber">[GENERATE]</h5>
                    </v-col>
                    <v-col cols="12" sm="5" lg="3" xl="5">
                        <v-text-field class="ml-15" label="Transaction Date" type="date" variant="plain"
                            v-model="transaction_date" />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" sm="6" lg="2" class="d-flex align-center">
                        <v-label>BARCODE:</v-label>
                    </v-col>
                    <v-col cols="12" sm="6" lg="3">
                        <SearchField class="ml-n6"></SearchField>
                    </v-col>
                    <v-col cols="12" sm="6" lg="3" class="d-flex align-center">
                        <v-btn class="text-blue clickable-text" block @click="showBrowseProductForm"
                            :disabled="!canBrowseProduct">[CLICK HERE TO BROWSE PRODUCT] </v-btn>
                    </v-col>
                    <!-- <v-col cols="12" xl="5" lg="3" class="d-flex align-center">
                        <v-card class="pa-3 total-card">
                            <v-row class="text-left">
                                <v-col v-for="(column, index) in headers" :key="index" cols="3">
                                    <span class="total-value">{{ calculateTotal(column.total) }}</span>
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-col> -->
                </v-row>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" :transaction_date="transaction_date"
                    :transaction_number="transaction_number" :transact_by="transact_by" item-value="id" class="elevation-1"
                    hide-default-footer fixed-header height="400">
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
                                    <span @click="openEditQuantityDialog(item)">{{ item.quantity_added }}</span>
                                </span>
                                <span v-else>{{ item.quantity_added }}</span>
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
                            <v-btn class="pagination-button" @click="previousPage"
                                :disabled="currentPage === 1">Previous</v-btn>

                            <v-btn v-for="pageNumber in totalPages" :key="pageNumber" @click="gotoPage(pageNumber)"
                                :class="{ active: pageNumber === currentPage }" class="pagination-button">
                                {{ pageNumber }}
                            </v-btn>

                            <v-btn class="pagination-button" @click="nextPage"
                                :disabled="currentPage === totalPages">Next</v-btn>
                        </div>
                    </template>
                </v-data-table>
                <!-- <CustomTable :columns="tableColumns" :items="products" :showMinusIcon="true" :showPlusCartIcon="true"  :isStockEntryPage="true"
                    @delete-data="deleteProductRow" :showDeleteIcon="true"  @edit-quantity="openEditQuantityDialog" height="450px" /> -->
            </v-col>
        </v-row>
        <!--Browse Product -->
        <v-row v-if="showBrowseProduct">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="8" md="8" lg="8" xl="10" class="form-container">
                        <BrowseProduct @close="closeBrowseProductForm" :addToCart="addToCartProduct" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <!-- Edit Quantity Dialog -->
        <v-dialog v-model="showEditQuantityDialog" max-width="400" class="center-dialog  no-background">
            <v-card>
                <v-card-title>Edit Quantity</v-card-title>
                <v-card-text>
                    <v-text-field v-model="editedQuantity" label="New Quantity"></v-text-field>
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <v-btn color="primary" @click="saveEditedQuantity"
                        :disabled="isEditQuantitySaveButtonDisabled">Save</v-btn>
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
        <v-row class="d-flex justify-space-between">
            <v-col cols="12" sm="6" lg="3" class="text-start">
                <v-btn to="/cashier-dashboard" color="success" block>BACK</v-btn>
            </v-col>
            <v-col cols="12" sm="6" lg="3" class="text-end">
                <v-btn color="success" block>SOLD</v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import BrowseProduct from '../../commons/BrowseProduct.vue';
import SearchField from '../../commons/SearchField.vue';
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
            showBrowseProduct: false,
            transaction_date: '',
            transaction_number: '',
            isGeneratingInvoiceNumber: false,
            editedQuantity: 0,
            editingIndex: -1,
            snackbar: false,
            isTransactionPage: true,
            snackbarColor: '',
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

        displayedIndex() {
            return (this.currentPage - 1) * this.itemsPerPage + 1;
        },
        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
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
    },

    methods: {
        generateAndFetchInvoiceNumber() {
            this.isGeneratingInvoiceNumber = true;
            axios.get('/transaction/generate-invoice-number')
                .then(response => {
                    this.transaction_number = response.data.transaction_number;
                })
                .catch(error => {
                    console.error('Error fetching invoice number', error);
                });
        },

        // calculateTotal(total) {
        //     return total;
        // },

        showBrowseProductForm() {
            this.showBrowseProduct = true;
        },

        closeBrowseProductForm() {
            this.showBrowseProduct = false;
        },

        calculateTotal(product) {
            const quantity = parseFloat(product.quantity_added);
            const price = parseFloat(product.price);

            if (!isNaN(quantity) && !isNaN(price)) {
                return quantity * price;
            } else {
                return 0;
            }
        },

        addToCartProduct(product) {
            const existingProduct = this.products.find(p => p.product_code === product.product_code);
            if (existingProduct) {
                existingProduct.quantity_added++;
                existingProduct.total = this.calculateTotal(existingProduct);
            } else {
                const newProduct = {
                    id: product.id,
                    product_code: product.product_code,
                    barcode: product.barcode,
                    description: product.description,
                    price: product.price,
                    quantity_added: 1,
                    total: product.price * 1, 
                };
                this.products.push(newProduct);
                this.loading.false;
            }
            this.totalItems = this.products.length;
        },

        openEditQuantityDialog(item) {
            this.editingIndex = this.products.indexOf(item);
            this.editedQuantity = item.quantity_added;
            this.showEditQuantityDialog = true;
        },

        saveEditedQuantity() {
            if (this.editingIndex !== -1) {
                const editedProduct = this.products[this.editingIndex];
                editedProduct.quantity_added = this.editedQuantity;

                editedProduct.total = this.calculateTotal(editedProduct);

                this.showEditQuantityDialog = false;
                this.editingIndex = -1;
                this.editedQuantity = 0;
                this.snackbarColor = 'success';
                this.showSnackbar('Quantity updated successfully', 'success');
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

        // deleteProductRow(product) {
        //     const index = this.products.findIndex(p => p.productCode === product.productCode);
        //     if (index !== -1) {
        //         this.products.splice(index, 1);
        //     }
        // },

        // openEditQuantityDialog(index) {
        //     // Set editingIndex and editedQuantity based on the clicked row
        //     this.editingIndex = index;
        //     this.editedQuantity = this.products[index].quantity;
        //     this.showEditQuantityDialog = true; // Open the dialog
        // },

        // saveEditedQuantity() {
        //     if (this.editingIndex !== -1) {
        //         this.products[this.editingIndex].quantity = this.editedQuantity;
        //         this.showEditQuantityDialog = false;
        //         this.editingIndex = -1;
        //         this.editedQuantity = 0;
        //     }
        // },

        // closeEditQuantityDialog() {
        //     this.showEditQuantityDialog = false;
        //     this.editingIndex = -1;
        //     this.editedQuantity = 0
        //         ;
        // },

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
    top: 10%;
    left: 1;
    right: 1;
    z-index: 999;

}

.center-dialog {
    position: fixed;
    left: 30%;
    right: 15%;
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