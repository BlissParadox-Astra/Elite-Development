<template>
    <v-container class="mt-5 section2">
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <CustomTable :columns="tableColumns" :items="paginatedProducts" height="500px" />
                <v-pagination v-model="currentPage" :length="totalPages" />
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '../../common/SearchField.vue';
import CustomTable from '../../common/CustomTable.vue';
import axios from 'axios'

export default {
    mixins: [CustomTable],
    name: 'InventoryListSection',

    components: {
        SearchField,
        CustomTable,
    },

    data() {
        return {
            currentPage: 1,
            itemsPerPage: 10,
            showForm: false,
            editingProduct: null,
            editingProductIndex: -1,
            products: [],
            tableColumns: [
                { key: 'product_code', label: 'Product Code' },
                { key: 'barcode', label: 'Barcode' },
                { key: 'description', label: 'Description' },
                { key: 'brand', label: 'Brand', render: this.renderProductBrand  },
                { key: 'category', label: 'Category', render: this.renderProductCategory },
                { key: 'price', label: 'Price' },
                { key: 'reorder_level', label: 'Reorder Level' },
                { key: 'stock_on_hand', label: 'Stock On Hand' },
            ],
        };
    },

    mounted() {
        this.getProducts();
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
        getProducts() { 
            axios.get('/products').then(res => {
                this.products = res.data.products
                // console.log(this.users)
            });
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
  