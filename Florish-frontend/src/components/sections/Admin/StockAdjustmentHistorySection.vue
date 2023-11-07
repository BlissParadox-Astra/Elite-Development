<template>
    <v-main>
        <v-container class="mt-5">
            <v-row>
                <v-col cols="12" sm="9">
                    <SearchField />
                </v-col>
            </v-row>
            <v-row justify="center">
                <v-col cols="12">
                    <v-data-table :headers="headers" :items="adjustments" :loading="loading" :page="currentPage"
                        :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1"
                        @update:options="getStockAdjustments" fixed-header height="400">
                        <template v-slot:custom-sort="{ header }">
                            <span v-if="header.key === 'actions'">Actions</span>
                        </template>
                        <template v-slot:item="{ item, index }">
                            <tr>
                                <td>{{ displayedIndex + index }}</td>
                                <td>{{ item.reference_number }}</td>
                                <td>{{ item.action }}</td>
                                <td>{{ item.adjusted_product.product_code }}</td>
                                <td>{{ item.adjusted_product.barcode }}</td>
                                <td>{{ item.adjusted_product.description }}</td>
                                <td>{{ item.remarks }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ item.stock_adjustment_by_user.first_name }}</td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-container>
    </v-main>
</template>

<script>
import SearchField from "../../commons/SearchField.vue";
import axios from 'axios';

export default {
    name: "StockAdjustmentHistory",
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
            adjustments: [],
            headers: [
                { title: '#', value: 'index' },
                { title: "Reference No.", key: "reference_number" },
                { title: "Action", key: "action" },
                { title: "Product Code", key: "adjusted_product.product_code" },
                { title: "Barcode", key: "adjusted_product.barcode" },
                { title: "Description", key: "adjusted_product.description" },
                { title: "Remarks", key: "remarks" },
                { title: 'Quantity', key: 'quantity' },
                { title: 'User', key: 'stock_adjustment_by_user.first_name' },
            ],
        };
    },

    computed: {
        displayedIndex() {
            return (this.currentPage - 1) * this.itemsPerPage + 1;
        },
    },

    async mounted() {
        await this.getStockAdjustments();
    },

    methods: {
        getStockAdjustments() {
            this.loading = true;
            axios
                .get('/stock-adjustments', {
                    params: {
                        page: this.currentPage,
                        itemsPerPage: this.itemsPerPage,
                    }
                })
                .then((res) => {
                    this.adjustments = res.data.stockAdjustments;
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                })
                .catch((error) => {
                    console.error('Error fetching stock adjustment records:', error);
                });
        },
        renderProductCode(adjusted_product) {
            return adjusted_product.adjusted_product ? adjusted_product.adjusted_product.product_code : 'Unknown';
        },

        renderProductBarcode(adjusted_product) {
            return adjusted_product.adjusted_product ? adjusted_product.adjusted_product.barcode : 'Unknown';
        },

        renderProductDescription(adjusted_product) {
            return adjusted_product.adjusted_product ? adjusted_product.adjusted_product.description : 'Unknown';
        },

        renderUser(stock_adjustment_by_user) {
            if (stock_adjustment_by_user.stock_adjustment_by_user) {
                const { first_name, last_name } = stock_adjustment_by_user.stock_adjustment_by_user;
                return `${first_name} ${last_name}`;
            } else {
                return 'Unknown';
            }
        },
    },
};
</script>
