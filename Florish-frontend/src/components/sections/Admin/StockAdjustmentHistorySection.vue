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
                    <CustomTable :columns="tableColumns" :items="paginatedProducts" height="500" />
                    <v-pagination v-model="currentPage" :length="totalPages" />
                </v-col>
            </v-row>
        </v-container>
    </v-main>
</template>

<script>
import SearchField from "../../common/SearchField.vue";
import CustomTable from "../../common/CustomTable.vue";
import axios from 'axios';

export default {
    name: "StockAdjustmentHistory",
    components: {
        SearchField,
        CustomTable,
    },
    data() {
        return {
            currentPage: 1,
            itemsPerPage: 10,
            adjustments: [],
            selectedRow: {
                referenceNo: "",
                action: "",
                productCode: "",
                barCode: "",
                description: "",
                remarks: "",
                quantity: "",
                user: "",
            },

            tableColumns: [
                { key: "reference_number", label: "Reference No." },
                { key: "action", label: "Action" },
                { key: "product_code", label: "Product Code", render: this.renderProductCode },
                { key: "barcode", label: "Barcode", render: this.renderProductBarcode },
                { key: "description", label: "Description", render: this.renderProductDescription },
                { key: "remarks", label: "Remarks" },
                { key: 'quantity', label: 'Quantity' },
                { key: 'user', label: 'User', render: this.renderUser },
            ],

        };
    },
    computed: {
        paginatedProducts() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.adjustments.slice(startIndex, endIndex);
        },
        totalPages() {
            return Math.ceil(this.adjustments.length / this.itemsPerPage);
        },
    },
    mounted() {
        this.getStockAdjustments();
    },

    methods: {
        async getStockAdjustments() {
            try {
                const response = await axios.get('/stockAdjustments');
                this.adjustments = response.data.stock_adjustments;
                console.log('Records:', this.adjustments);
            } catch (error) {
                console.error('Error fetching stock in records:', error);
            }
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
    }

};
</script>
