<template>
    <v-container class="mt-5 section2">
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1"
                    @update:options="getCriticalStocks" fixed-header height="400">
                    <template v-slot:custom-sort="{ header }">
                        <span v-if="header.key === 'actions'">Actions</span>
                    </template>
                    <template v-slot:item="{ item, index }">
                        <tr>
                            <td>{{ displayedIndex + index }}</td>
                            <td>{{ item.product_code }}</td>
                            <td>{{ item.barcode }}</td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.brand.brand_name }}</td>
                            <td>{{ item.category.category_name }}</td>
                            <td>{{ item.price }}</td>
                            <td>{{ item.reorder_level }}</td>
                            <td>{{ item.stock_on_hand }}</td>
                        </tr>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '../../commons/SearchField.vue';
import axios from 'axios';

export default {
    name: 'CriticalStockSection',

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
            editingProductIndex: -1,
            products: [],
            headers: [
                { title: '#', key: 'id' },
                { title: 'Product Code', key: 'product_code' },
                { title: 'Barcode', key: 'barcode' },
                { title: 'Description', key: 'description' },
                { title: 'Brand', key: 'brand.brand_name' },
                { title: 'Category', key: 'category.category_name' },
                { title: 'Price', key: 'price' },
                { title: 'Reorder Level', key: 'reorder_level' },
                { title: 'Stock On Hand', key: 'stock_on_hand' },
            ],
        };
    },

    computed: {
        displayedIndex() {
            return (this.currentPage - 1) * this.itemsPerPage + 1;
        },
    },

    async mounted() {
        await this.getCriticalStocks();
    },

    methods: {
        getCriticalStocks() {
            this.loading = true;
            axios
                .get('/critical-stocks', {
                    params: {
                        page: this.currentPage,
                        itemsPerPage: this.itemsPerPage,
                    }
                })
                .then((res) => {
                    this.products = res.data.criticalStocks;
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                })
                .catch((error) => {
                    console.error('Error fetching products:', error);
                });
        }
    },

    renderProductCategory(category) {
        return category.category ? category.category.category_name : 'Unknown';
    },

    renderProductBrand(brand) {
        return brand.brand ? brand.brand.brand_name : 'Unknown';
    },

};
</script>
  
<style scoped>
/* Add any scoped styles here */
</style>
  