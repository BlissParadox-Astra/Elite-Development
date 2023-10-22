<template>
    <v-container class="mt-5 section2">
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table-server v-model:items-per-page="itemsPerPage" :page="page" :headers="headers"
                    :items-length="totalItems" :items="products" :loading="loading" item-value="id" class="elevation-1"
                    @update:options="getProducts">
                    <template v-slot:custom-sort="{ header }">
                        <span v-if="header.key === 'actions'">Actions</span>
                    </template>
                    <template v-slot:item="{ item }">
                        <tr>
                            <td>{{ item.id }}</td>
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
                </v-data-table-server>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '../../commons/SearchField.vue';
import axios from 'axios'

export default {
    name: 'InventoryListSection',

    components: {
        SearchField,
    },

    data() {
        return {
            itemsPerPage: 10,
            page: 1,
            id: 1,
            loading: true,
            totalItems: 0,
            showForm: false,
            editingProduct: null,
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

    async mounted() {
        await this.getProducts();
    },

    methods: {
        getProducts() {
            axios
                .get('/products', {
                    params: {
                        page: this.page,
                        itemsPerPage: this.itemsPerPage,
                    }
                }).then((res) => {
                    this.products = [...res.data.products.data];
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                })
                .catch((error) => {
                    console.error('Error fetching products:', error);
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
  