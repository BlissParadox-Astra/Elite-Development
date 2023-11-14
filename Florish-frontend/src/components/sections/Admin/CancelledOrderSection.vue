<template>
    <v-container class="mt-5 section2">
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="canceled_orders" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
                    @update:options="debouncedGetCanceledOrders" fixed-header>
                    <template v-slot:custom-sort="{ header }">
                        <span v-if="header.key === 'actions'">Actions</span>
                    </template>
                    <template v-slot:item="{ item, index }">
                        <tr>
                            <td>{{ displayedIndex + index }}</td>
                            <td>{{ item.canceled_transaction.transaction_number }}</td>
                            <td>{{ item.canceled_transaction.transacted_product.product_code }}</td>
                            <td>{{ item.canceled_transaction.transacted_product.barcode }}</td>
                            <td>{{ item.canceled_transaction.transacted_product.description }}</td>
                            <td>{{ item.canceled_transaction.transacted_product.price }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.total }}</td>
                            <td>{{ item.canceled_date }}</td>
                            <td>{{ item.user.first_name }}</td>
                            <td>{{ item.reason }}</td>
                            <td>{{ item.action_taken }}</td>
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
    </v-container>
</template>
  
<script>
import SearchField from '../../commons/SearchField.vue';
import _debounce from 'lodash/debounce';
import axios from 'axios';

export default {
    name: 'CancelOrderSection',
    components: {
        SearchField,
    },

    data() {
        return {
            itemsPerPage: 10,
            currentPage: 1,
            id: 1,
            totalItems: 0,
            loading: true,
            showForm: false,
            canceled_orders: [],
            editingProduct: null,
            editingProductIndex: -1,
            headers: [
                { title: '#', value: 'index' },
                { title: 'Reference No.', key: 'canceled_transaction.transaction_number' },
                { title: 'Product Code', key: 'canceled_transaction.transacted_product.product_code' },
                { title: 'Barcode', key: 'canceled_transaction.transacted_product.barcode' },
                { title: 'Description', key: 'canceled_transaction.transacted_product.description' },
                { title: 'Price', key: 'canceled_transaction.transacted_product.price' },
                { title: 'Quantity', key: 'quantity' },
                { title: 'Total', key: 'total' },
                { title: 'Date', key: 'canceled_date' },
                { title: 'Canceled By', key: 'user.first_name' },
                { title: 'Reason', key: 'reason' },
                { title: 'Action', key: 'action' },
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
        await this.debouncedGetCanceledOrders();
    },

    methods: {
        debouncedGetCanceledOrders: _debounce(function () {
            this.getCanceledOrders();
        }, 3000),

        getCanceledOrders() {
            this.loading = true;
            axios
                .get('/canceled-orders', {
                    params: {
                        page: this.currentPage,
                        itemsPerPage: this.itemsPerPage,
                    }
                })
                .then((res) => {
                    this.canceled_orders = res.data.canceled_orders;
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                });
        },
        previousPage() {
            this.loading = true;
            if (this.currentPage > 1) {
                this.currentPage--;
                this.debouncedGetCanceledOrders();
            }
        },

        nextPage() {
            this.loading = true;
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.debouncedGetCanceledOrders();
            }
        },

        gotoPage(pageNumber) {
            this.loading = true;
            this.currentPage = pageNumber;
            this.debouncedGetCanceledOrders();
        },
        
        renderReferenceNUmber(canceled_order) {
            return canceled_order.canceled_transaction ? canceled_order.canceled_transaction.transaction_number : 'Unknown';
        },

        renderProductCode(canceled_order) {
            return canceled_order.canceled_transaction && canceled_order.canceled_transaction.transacted_product
                ? canceled_order.canceled_transaction.transacted_product.product_code
                : 'Unknown';
        },

        renderBarCode(canceled_order) {
            return canceled_order.canceled_transaction && canceled_order.canceled_transaction.transacted_product
                ? canceled_order.canceled_transaction.transacted_product.barcode
                : 'Unknown';
        },

        renderPrice(canceled_order) {
            return canceled_order.canceled_transaction && canceled_order.canceled_transaction.transacted_product
                ? canceled_order.canceled_transaction.transacted_product.price
                : 'Unknown';
        },

        renderDescription(canceled_order) {
            return canceled_order.canceled_transaction && canceled_order.canceled_transaction.transacted_product
                ? canceled_order.canceled_transaction.transacted_product.description
                : 'Unknown';
        },

        renderUser(user) {
            if (user.user) {
                const { first_name, last_name } = user.user;
                return `${first_name} ${last_name}`;
            } else {
                return 'Unknown';
            }
        },
    },
};
</script>
<style scoped>
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
  