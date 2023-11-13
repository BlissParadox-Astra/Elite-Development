<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12">
                        <h2 class="text-center">SOLD PURCHASE</h2>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" sm="12" lg="9" md="12">
                        <FilterByDate></FilterByDate>
                    </v-col>
                    <v-col cols="12" sm="12" lg="3" md="12">
                        <v-card class="pa-3 total-card">
                            <v-row class="text-left">
                                <v-col cols="6">
                                    <span class="total-value" v-if="totalOfAllTotalValue !== null">{{ totalOfAllTotalValue
                                    }}</span>
                                    <span class="loading-message" v-else>Loading...</span>
                                </v-col>
                            </v-row>
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
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="8" md="8" lg="8" xl="10" class="form-container">
                        <CancelOrderForm v-if="showForm" @close="hideCancelOrderForm" @cancel-order = "cancelOrder"/>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-row class="d-flex justify-space-between">
            <v-col cols="12" sm="6" lg="3" class="text-start">
                <v-btn to="/cashier-dashboard" color="success" block>BACK</v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import FilterByDate from "../../commons/FilterByDate.vue";
import CancelOrderForm from "../../forms/CancelOrderForm.vue";
import _debounce from 'lodash/debounce';
import axios from "axios";
export default {
    name: 'transactionCart',
    components: {
        FilterByDate,
        CancelOrderForm,
    },
    data() {
        return {
            itemsPerPage: 10,
            currentPage: 1,
            totalItems: 0,
            soldTransaction: -1,
            // CancelOrderForm: false,
            loading: true,
            totalOfAllTotalValue: null,
            showForm: false,
            transactions: [],
            headers: [
                { title: '#', value: 'index' },
                { title: "Invoice No.", key: 'transaction_number' },
                { title: "Product Code", key: 'transacted_product.product_code' },
                { title: "Barcode", key: 'transacted_product.barcode' },
                { title: "Description", key: 'transacted_product.description' },
                { title: "Price", key: 'price' },
                { title: "Quantity", key: 'quantity' },
                { title: "Total", key: 'total' },
                { title: "Transacted Date", key: 'transaction_date' },
                { title: "Transacted By", key: 'user.first_name' },
                { title: 'Actions', key: 'actions', sortable: false }
            ],
        };

    },

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
        }, 3000),

        getTransactions() {
            this.loading = true;
            axios
                .get('/transactions', {
                    params: {
                        page: this.currentPage,
                        itemsPerPage: this.itemsPerPage,
                    }
                })
                .then((res) => {
                    this.transactions = res.data.transactions;
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                });
        },

        fetchOrder(transaction) {
            this.soldTransaction = {
                ...transaction,
                id: transaction.id,
            }
            const index = this.transactions.findIndex(t => t.id === transaction.id);
            this.soldTransaction = index;
            this.showForm = true;
        },

        async cancelOrder() {

        },


        async fetchTotalOfAllTotal() {
            try {
                const response = await axios.get('/all-transactions-total');
                this.totalOfAllTotalValue = response.data.total;
            } catch (error) {
                console.error('Error fetching total of all total', error);
                this.totalOfAllTotalValue = 0;
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
    background-color: #23b78d;
    color: #fff;
    border-color: #007bff;
}
</style>
