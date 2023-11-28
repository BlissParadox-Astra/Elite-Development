<template>
    <v-container class="mt-14">
        <v-row justify="center">
            <v-col cols="12">
                <v-row>
                    <v-col cols="12" sm="8">
                        <FilterByDate @date-range-change="handleDateRangeChange"
                            @filter-type-change="handleFilterTypeChange" />
                    </v-col>
                    <v-col cols="12" sm="9">
                        <SearchField @search="handleSearch" />
                    </v-col>
                    <!-- <v-col cols="12" sm="4">
                        <v-btn color="#23b78d" block>
                            SORT BY
                            <v-menu activator="parent">
                                <v-list>
                                    <v-list-item v-for="(item, index) in items" :key="index" :value="index"
                                        @click="updateSort(item.title)">
                                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </v-btn>
                    </v-col> -->
                    <v-col cols="12" sm="2">
                        <v-card class="pa-3 total-card">
                            <span class="total-label">Total: </span>
                            <span class="loading-message" v-if="loadingTotal">Loading...</span>
                            <span class="total-value" v-else-if="totalOfAllTotalValue !== null">{{ totalOfAllTotalValue
                            }}</span>
                            <span class="loading-message" v-else>Loading...</span>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="transactions" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
                    @update:options="debouncedGetTransactions" fixed-header height="400">
                    <template v-slot:custom-sort="{ header }">
                        <span v-if="header.key === 'actions'">Actions</span>
                    </template>
                    <template v-slot:item="{ item, index }">
                        <tr>
                            <td>{{ displayedIndex + index }}</td>
                            <td>{{ item.transaction_number }}</td>
                            <td>{{ item.transacted_product.product_code }}</td>
                            <td>{{ item.transacted_product.barcode }}</td>
                            <td>{{ item.transacted_product.description }}</td>
                            <td>{{ item.transacted_product.category.category_name }}</td>
                            <td>{{ item.price }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.total }}</td>
                            <td>{{ item.transaction_date }}</td>
                            <td>{{ item.user.first_name }}</td>
                            <td>
                                <span>
                                    <v-icon @click="fetchOrder(item)">mdi-arrow-right</v-icon>
                                </span>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:bottom>
                        <div class="text-center pt-8 pagination">
                            <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                                :disabled="currentPage === 1">Previous</v-btn>

                            <v-btn v-for="pageNumber in totalPages" :key="pageNumber" @click="gotoPage(pageNumber)"
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
        <v-row>
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="8" md="8" lg="8" xl="10" class="form-container">
                        <CancelOrderForm v-if="showForm" @close="hideCancelOrderForm" @cancel-order="cancelOrder"
                            :initialTransaction="soldTransaction" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-row class="d-flex justify-space-between">
            <v-col cols="12" sm="6" lg="3" class="text-start">
                <v-btn to="/cashier-dashboard" color="#23b78d" block>BACK</v-btn>
            </v-col>
        </v-row>

        <v-snackbar v-model="snackbar" right top :color="snackbarColor">
            {{ snackbarText }}
            <template v-slot:actions>
                <v-btn color="pink" variant="text" @click="snackbar = false">
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </v-container>
</template>
<script>
import FilterByDate from "../../commons/FilterByDate.vue";
import SearchField from '../../commons/SearchField.vue';
import CancelOrderForm from "../../forms/CancelOrderForm.vue";
import _debounce from 'lodash/debounce';
import axios from "axios";
export default {
    name: 'soldPurchased',
    components: {
        SearchField,
        FilterByDate,
        CancelOrderForm,
    },
    data() {
        return {
            itemsPerPage: 10,
            currentPage: 1,
            totalItems: 0,
            soldTransaction: null,
            soldTransactionIndex: -1,
            loading: true,
            loadingTotal: false,
            totalOfAllTotalValue: null,
            showForm: false,
            transactions: [],
            snackbar: false,
            snackbarColor: '',
            fromDate: '',
            toDate: '',
            filterType: '',
            searchQuery: '',
            // selectedSort: 'Alphabetically',
            headers: [
                { title: '#', value: 'index' },
                { title: "Invoice No.", key: 'transaction_number' },
                { title: "Product Code", key: 'transacted_product.product_code' },
                { title: "Barcode", key: 'transacted_product.barcode' },
                { title: "Description", key: 'transacted_product.description' },
                { title: "Category", key: 'transacted_product.category.category_name' },
                { title: "Price", key: 'price' },
                { title: "Quantity", key: 'quantity' },
                { title: "Total", key: 'total' },
                { title: "Transacted Date", key: 'transaction_date' },
                { title: "Transacted By", key: 'user.first_name' },
                { title: 'Actions', key: 'actions', sortable: false }
            ],
            // items: [
            //     { title: 'Category' },
            //     { title: 'Total' },
            //     { title: 'Alphabetically' },
            // ],
        };
    },

    // watch: {
    //     selectedSort: {
    //         handler: function (newSort, oldSort) {
    //             if (newSort !== oldSort) {
    //                 this.currentPage = 1;
    //                 this.debouncedGetTransactions();
    //             }
    //         },
    //         immediate: true,
    //     },
    // },

    computed: {
        displayedIndex() {
            return (this.currentPage - 1) * this.itemsPerPage + 1;
        },

        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },
    },

    async mounted() {
        await this.debouncedGetTransactions();
        await this.fetchTotalOfAllTotal();
    },

    methods: {
        debouncedGetTransactions: _debounce(function () {
            this.getTransactions();
        }, 1000),

        handleSearch(query) {
            this.searchQuery = query;
            this.currentPage = 1;
            this.debouncedGetTransactions();
        },

        async getTransactions() {
            this.loading = true;
            this.loadingTotal = true;
            try {
                let params = {
                    page: this.currentPage,
                    itemsPerPage: this.itemsPerPage,
                    fromDate: this.fromDate,
                    toDate: this.toDate,
                    // sortBy: this.selectedSort,
                    search: this.searchQuery,
                    userId: this.$store.state.user.id,
                };

                if (this.filterType) {
                    switch (this.filterType) {
                        case 'Day':
                            params.filterType = 'Day';
                            break;
                        case 'Week':
                            params.filterType = 'Week';
                            break;
                        case 'Month':
                            params.filterType = 'Month';
                            break;
                        case 'Year':
                            params.filterType = 'Year';
                            break;
                        case 'Customize':
                            params.filterType = 'Customize';
                            break;
                    }
                }

                const response = await axios.get('/transactions', { params });

                this.transactions = response.data.transactions;
                this.totalItems = response.data.totalItems;
                this.loading = false;

                await this.fetchTotalOfAllTotal();
            } catch (error) {
                console.error('Error fetching transactions:', error);
                this.loading = false;
            } finally {
                this.loadingTotal = false;
            }
        },

        async fetchTotalOfAllTotal() {
            try {
                this.loadingTotal = true;
                let params = {
                    fromDate: this.fromDate,
                    toDate: this.toDate,
                    userId: this.$store.state.user.id,
                    search: this.searchQuery,
                };

                switch (this.filterType) {
                    case 'Day':
                        params.filterType = 'Day';
                        break;
                    case 'Week':
                        params.filterType = 'Week';
                        break;
                    case 'Month':
                        params.filterType = 'Month';
                        break;
                    case 'Year':
                        params.filterType = 'Year';
                        break;
                    case 'Customize':
                        params.filterType = 'Customize';
                        break;
                }

                const response = await axios.get('/all-transactions-total', { params });
                this.totalOfAllTotalValue = response.data.total;
            } catch (error) {
                console.error('Error fetching total of all total', error);
                this.totalOfAllTotalValue = 0;
            } finally {
                this.loadingTotal = false;
            }
        },

        handleFilterTypeChange(newFilterType) {
            this.filterType = newFilterType;
            this.currentPage = 1;
            this.debouncedGetTransactions();
        },

        // updateSort(sortType) {
        //     this.selectedSort = sortType;
        // },

        handleDateRangeChange({ fromDate, toDate }) {
            this.fromDate = fromDate;
            this.currentPage = 1;
            this.toDate = toDate;
            this.debouncedGetTransactions();
        },

        fetchOrder(transaction) {
            this.soldTransaction = {
                ...transaction,
                id: transaction.id,
            }
            const index = this.transactions.findIndex(t => t.id === transaction.id);
            this.soldTransactionIndex = index;
            this.showForm = true;
        },

        async cancelOrder(transactionData) {
            try {
                const response = await axios.post('/cancel-order', transactionData);
                if (response.status === 200) {
                    this.transactions.push(response.data);
                    this.hideCancelOrderForm();
                    this.snackbarColor = 'success';
                    this.showSnackbar(response.data.message, 'success');
                    this.getTransactions();
                    this.fetchTotalOfAllTotal();
                } else {
                    this.snackbarColor = 'error';
                    this.showSnackbar(response.data.message, 'error');
                }
            } catch (error) {
                console.error('Error adding product:', error);
                if (error.response && error.response.status === 500) {
                    this.snackbarColor = 'error';
                    this.showSnackbar(error.response.data.error || 'An error occurred.', 'error');
                } else {
                    this.snackbarColor = 'error';
                    this.showSnackbar(error.response.data.message, 'error');
                }
            }
        },

        previousPage() {
            this.loading = true;
            if (this.currentPage > 1) {
                this.currentPage--;
                this.debouncedGetTransactions();
            }
        },

        nextPage() {
            this.loading = true;
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.debouncedGetTransactions();
            }
        },

        gotoPage(pageNumber) {
            this.loading = true;
            this.currentPage = pageNumber;
            this.debouncedGetTransactions();
        },

        renderProductCode(transacted_product) {
            return transacted_product.transacted_product ? transacted_product.transacted_product.product_code : 'Unknown';
        },

        renderBarCode(transacted_product) {
            return transacted_product.transacted_product ? transacted_product.transacted_product.barcode : 'Unknown';
        },

        renderDescription(transacted_product) {
            return transacted_product.transacted_product ? transacted_product.transacted_product.description : 'Unknown';
        },

        renderProductCategory(transactions) {
            if (transactions.transacted_product && transactions.transacted_product.category) {
                return transactions.transacted_product.category.category_name;
            } else {
                return 'Unknown';
            }
        },

        renderUser(user) {
            if (user.user) {
                const { first_name, last_name } = user.user;
                return `${first_name} ${last_name}`;
            } else {
                return 'Unknown';
            }
        },

        calculateTotal(total) {
            return total;
        },

        showCancelOrderForm() {
            this.showForm = true;
        },

        hideCancelOrderForm() {
            this.showForm = false;
        },

        closeCancelOrderForm() {
            this.showForm = false;
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
    }
}
</script>

<style scoped>
.form-container {
    position: absolute;
    top: 0;
    left: 1;
    right: 1;
    z-index: 999;
    max-height: 100%;
    overflow-y: auto;
}

.total-value {
    font-weight: bold;
    color: #333;
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
    /* background-color: #23b78d; */
    color: #fff;
    background-color: #007bff;
}
</style>
