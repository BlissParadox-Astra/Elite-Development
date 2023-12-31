<template>
    <v-container class="mt-12">
        <v-row>
            <v-col cols="12">
                <v-row>
                    <v-col cols="12" md="4" lg="4" sm="5" class="mt-3">
                        <v-text-field class="ml-1" label="Transaction ID" variant="plain" v-model="transaction_number"
                            readonly />
                    </v-col>

                    <v-col cols="12" md="2" lg="3" sm="3" class="d-flex align-center">
                        <v-btn class="text clickable-text" color="#23b78d" block @click="generateAndFetchInvoiceNumber"
                            :disabled="isGeneratingInvoiceNumber">GENERATE</v-btn>
                    </v-col>

                    <v-col cols="12" md="5" lg="4" sm="4" class="mt-3">
                        <v-text-field label="Transaction Date" type="text" variant="plain" v-model="transaction_date"
                            readonly />
                    </v-col>

                    <v-col cols="12" md="4" lg="4" sm="5">
                        <SearchField ref="barcodeSearchField" @searchBarcode="handleBarcodeScan" :searchLabel="searchLabel"
                            :searchType="'barcode'" :disabled="!isTransactionNumberPresent" />
                    </v-col>

                    <v-col cols="12" md="2" lg="3" sm="3" class="d-flex align-center">
                        <v-btn class="text clickable-text" color="#23b78d" block @click="showBrowseProductForm"
                            :disabled="!canBrowseProduct">BROWSE PRODUCT</v-btn>
                    </v-col>

                    <v-row class="">
                        <v-col cols="12" class="text-center">
                            <h2 class="total-text">
                                <span class="total-label">Total:</span>
                                <span class="total-value">₱{{ calculateOverallTotal().toFixed(2) }}</span>
                            </h2>
                        </v-col>
                    </v-row>

                    <v-col cols="12" md="8" lg="9" sm="12">
                        <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
                            :items-per-page="itemsPerPage" density="compact" :transaction_date="transaction_date"
                            :transaction_number="transaction_number" :transact_by="transact_by" item-value="id"
                            class="elevation-1" hide-default-footer fixed-header height="350">
                            <template v-slot:custom-sort="{ header }">
                                <span v-if="header.key === 'actions'">Actions</span>
                            </template>
                            <template v-slot:item="{ item, index }">
                                <tr>
                                    <td>{{ displayedIndex + index }}</td>
                                    <td>{{ item.product_code }}</td>
                                    <td>{{ item.barcode }}</td>
                                    <td>{{ item.description }}</td>
                                    <td>₱{{ item.price }}</td>
                                    <td>
                                        <span v-if="isTransactionPage">
                                            <span @click="openEditQuantityDialog(item)">{{ item.quantity }}</span>
                                        </span>
                                        <span v-else>{{ item.quantity }}</span>
                                    </td>
                                    <td>₱{{ item.total }}</td>
                                    <td>
                                        <span>
                                            <v-icon @click="subtractProduct(item)" size="22" class="ml-n3"
                                                color="#23b78d">mdi-minus-circle</v-icon>
                                        </span>
                                        <span>
                                            <v-icon @click="addProduct(item)" size="22"
                                                color="#23b78d">mdi-plus-circle</v-icon>
                                        </span>
                                        <span>
                                            <v-icon @click="showDeleteConfirmation(item)" color="#23b78d"
                                                size="22">mdi-delete</v-icon>
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

                                        <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber"
                                            @click="gotoPage(pageNumber)" :class="{ active: pageNumber === currentPage }"
                                            class="pagination-button">
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
                    </v-col>
                    <v-col cols="12" md="4" lg="3" sm="12">
                        <v-card class="pa-2 total-card" style="height: 430px;" color="#23b78d">
                            <v-row class="text-left" style="height: 12%;" align="center">
                                <v-col cols="12">
                                    <v-card-title class="headline white--text font-weight-bold">Transaction
                                        Summary</v-card-title>
                                </v-col>
                                <v-col cols="12" class="mb-2 mt-n7">
                                    <v-card-text>Over All Total</v-card-text>
                                    <v-text-field v-model="overallTotal" readonly dense outlined class="input-lg">
                                        ₱{{ calculateOverallTotal().toFixed(2) }}
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" class="mb-2 mt-n12">
                                    <v-card-text>Payment Received</v-card-text>
                                    <v-text-field v-model="payment" dense outlined @input="calculateChange">₱</v-text-field>
                                </v-col>
                                <v-col cols="12" class="mb-2 mt-n12">
                                    <v-card-text>Change</v-card-text>
                                    <v-text-field v-model="change" readonly dense outlined class="input-lg">₱</v-text-field>
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-col>
                    <v-row justify="space-between" class="pa-3">
                        <v-col cols="12" md="5" sm="12" lg="2" class="text-start ">
                            <v-btn @click="showBackConfirmation" color="#23b78d" block>BACK</v-btn>
                        </v-col>
                        <v-col cols="12" md="5" sm="12" lg="2" class="text-end ">
                            <v-btn color="#23b78d" @click="showConfirmation"
                                :disabled="!isPaymentEnough || isSoldButtonDisabled" block>
                                SOLD
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-row>
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
        <!-- Confirmation dialog for back button -->
        <v-dialog v-model="showBackConfirmationDialog" max-width="400" class="center-dialog  no-background">
            <v-card>
                <v-card-title class="bg-teal pa-1 text-center">
                    Confirm Navigation
                </v-card-title>
                <v-card-text class="text-center">
                    ARE YOU SURE YOU WANT TO LEAVE THIS TRANSACTION?
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <div>
                        <v-btn color="#23b78d" @click="navigateBack" style="width: 150px;">Yes</v-btn>
                        <v-btn color="#068863" @click="cancelNavigation">No</v-btn>
                    </div>
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
        <v-row class="mt-n4">
            <v-dialog v-model="showConfirmationDialog" max-width="400" class="center-dialog no-background">
                <v-card>
                    <v-card-title class="bg-teal pa-2 text-center white--text">
                        Confirm Transaction
                    </v-card-title>
                    <v-card-text class="text-center">
                        ARE YOU SURE YOU WANT TO COMPLETE THIS TRANSACTION?

                        <v-divider class="my-3"></v-divider>

                        <div class="confirmation-details">
                            <div>
                                <strong>Overall Total:</strong> ₱{{ confirmationDetails.overallTotal }}
                            </div>

                            <div>
                                <strong>Payment Received:</strong> ₱{{ confirmationDetails.paymentReceived }}
                            </div>

                            <div>
                                <strong>Change:</strong> ₱{{ confirmationDetails.change }}
                            </div>
                        </div>
                    </v-card-text>
                    <v-card-actions class="d-flex justify-center">
                        <div>
                            <v-btn color="#23b78d" @click="saveRecord" class="mr-2">Yes</v-btn>
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
            change: 0,
            showEditQuantityDialog: false,
            showConfirmationDialog: false,
            showBrowseProduct: false,
            showBackConfirmationDialog: false,
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
            overallTotal: '',
            payment: '',
            loading: false,
            products: [],
            confirmationDetails: {
                overallTotal: 0,
                paymentReceived: 0,
                change: 0,
            },
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
            return this.editedQuantity <= 0 || isNaN(this.editedQuantity);
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

        isPaymentEnough() {
            const payment = parseFloat(this.payment);
            const overallTotal = parseFloat(this.calculateOverallTotal());
            return !isNaN(payment) && payment >= 0 && payment >= overallTotal;
        },
    },

    mounted() {
        this.updateTransactionDate();

        this.intervalId = setInterval(() => {
            this.updateTransactionDate();
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
        this.generateAndFetchInvoiceNumber();
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

        updateTransactionDate() {
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

            this.transaction_date = formattedDate;
        },

        filterNumeric(event) {
            const keyCode = event.keyCode || event.which;
            const key = String.fromCharCode(keyCode);

            if (/^\d+$/.test(key)) {
                return;
            }

            event.preventDefault();
        },

        generateAndFetchInvoiceNumber() {
            this.isGeneratingInvoiceNumber = true;
            axios.get('/transaction/generate-transaction-number')
                .then(response => {
                    this.transaction_number = response.data.transaction_number;
                    this.currentPage = 1;
                })
                .catch(error => {
                    console.error('Error fetching invoice number', error);
                });
        },

        async addToCartProduct(product) {
            const existingProduct = this.products.find(p => p.product_code === product.product_code);

            if (existingProduct) {

                if (existingProduct.quantity >= product.stock_on_hand) {
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
                    stock_on_hand: product.stock_on_hand,
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
            this.$refs.barcodeSearchField.$refs.searchField.focus();
        },

        addProduct(item) {
            if (item.quantity >= item.stock_on_hand) {
                this.showSnackbar('Not enough stock available for this product', 'error');
                return;
            }

            item.quantity++;
            item.total = this.calculateTotal(item);

            this.$refs.barcodeSearchField.$refs.searchField.focus();
        },

        calculateChange() {
            const payment = parseFloat(this.payment);
            const overallTotal = parseFloat(this.calculateOverallTotal());

            if (!isNaN(payment) && payment >= 0 && payment >= overallTotal) {
                this.change = (payment - overallTotal).toFixed(2);
                this.confirmationDetails.paymentReceived = payment.toFixed(2);
                this.confirmationDetails.change = this.change;
            } else {
                this.change = '0.00';
                this.confirmationDetails.paymentReceived = '0.00';
                this.confirmationDetails.change = '0.00';
            }
        },

        calculateOverallTotal() {
            const overallTotal = this.products.reduce((total, product) => total + parseFloat(product.total), 0);
            this.confirmationDetails.overallTotal = overallTotal.toFixed(2);
            return overallTotal;
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

        async saveEditedQuantity() {
            if (this.editingIndex !== -1) {
                const editedProduct = this.products[this.editingIndex];

                if (this.editedQuantity > 0) {
                    if (this.editedQuantity > editedProduct.stock_on_hand) {
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
                    this.$nextTick(() => {
                        this.$refs.barcodeSearchField.$refs.searchField.focus();
                    });
                } else {
                    this.snackbarColor = 'error';
                    this.showSnackbar('Quantity must be greater than 0', 'error');
                }
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

        resetPayment() {
            this.payment = '';
            this.change = 0;
        },

        saveRecord() {
            this.showConfirmationDialog = false;
            const transactionRequests = this.products.map((product) => {
                const overallTotal = this.calculateOverallTotal();
                return {
                    transaction_number: this.transaction_number,
                    transaction_date: this.transaction_date,
                    product_id: product.id,
                    quantity: product.quantity,
                    transaction_total: overallTotal,
                    payment: this.payment,
                    change: this.change,
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
                    this.resetPayment();
                    this.$refs.barcodeSearchField.$refs.searchField.focus();
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
            this.confirmationDetails.totalItems = this.products.length;
        },

        cancelSave() {
            this.showConfirmationDialog = false;
            this.resetPayment();
        },

        showBrowseProductForm() {
            this.showBrowseProduct = true;
        },

        showBackConfirmation() {
            this.showBackConfirmationDialog = true;
        },

        navigateBack() {
            this.$router.push('/cashier-dashboard');
            this.showBackConfirmationDialog = false;
        },

        cancelNavigation() {
            this.showBackConfirmationDialog = false;
        },

        closeBrowseProductForm() {
            this.showBrowseProduct = false;
            this.$nextTick(() => {
                this.$refs.barcodeSearchField.$refs.searchField.focus();
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
    font-size: 45px;
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
    background-color: #23b78d;
    color: #fff;
    border-color: #23b78d;
}

.total-text {
    font-size: 1.5em;
}

.total-label {
    font-weight: bold;
    margin-right: 40px;
}

.confirmation-details {
    margin-top: 20px;
}

.confirmation-details>div {
    margin-bottom: 10px;
}

.center-dialog {
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-teal {
    background-color: #23b78d;
}

.white--text {
    color: white;
}

.text-center {
    text-align: center;
}

.my-3 {
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
}

.d-flex {
    display: flex;
}

.justify-center {
    justify-content: center;
}

.mr-2 {
    margin-right: 0.5rem;
}
</style>