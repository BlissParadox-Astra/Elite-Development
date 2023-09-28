<template>
    <v-container class="mt-5 section2">
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <CustomTable v-if="products.length > 0" :columns="tableColumns" :items="paginatedProducts" height="500" />
                <v-pagination v-if="products.length > 0" v-model="currentPage" :length="totalPages" />
                <div v-else>No critical products available.</div>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '../../common/SearchField.vue';
import CustomTable from '../../common/CustomTable.vue';
import axios from 'axios';

export default {
    name: 'CriticalStockSection',

    components: {
        SearchField,
        CustomTable,
    },

    data() {
        return {
            currentPage: 1,
            itemsPerPage: 10,
            showForm: false,
            editingProductIndex: -1,
            products: [],
            tableColumns: [
                { key: 'product_code', label: 'Product Code' },
                { key: 'barcode', label: 'Barcode' },
                { key: 'description', label: 'Description' },
                { key: 'brand', label: 'Brand', render: this.renderProductBrand },
                { key: 'category', label: 'Category', render: this.renderProductCategory },
                { key: 'price', label: 'Price' },
                { key: 'reorder_level', label: 'Reorder Level' },
                { key: 'stock_on_hand', label: 'Stock On Hand' },
            ],
        };
    },

    mounted() {
        this.getCriticalStocks();
    },
    computed: {
        paginatedProducts() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.products.slice(startIndex, endIndex);
        },
        totalPages() {
            return Math.ceil(this.products.length / this.itemsPerPage);
        },
    },
    methods: {
        async getCriticalStocks() {
            try {
                const response = await axios.get('/products/critical-stocks');
                this.products = response.data;
                console.log('Products:', this.products);
            } catch (error) {
                console.error('Error fetching critical stocks:', error);
            }
        },

        renderProductCategory(category) {
            return category.category ? category.category.category_name : 'Unknown';
        },

        renderProductBrand(brand) {
            return brand.brand ? brand.brand.brand_name : 'Unknown';
        },

    }
};
</script>
  
<style scoped>
/* Add any scoped styles here */
</style>
  