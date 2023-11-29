<template>
    <v-container class="section2 mt-14">
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField @search="handleSearch" :searchType="'regular'" />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
                    @update:options="debouncedGetProducts" fixed-header height="400">
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
                    <template v-slot:bottom>
                        <div class="text-center pt-5 pagination">
                            <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                                :disabled="currentPage === 1">Previous</v-btn>

                            <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
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
    </v-container>
</template>
  
<script>
import SearchField from '../../commons/SearchField.vue';
import _debounce from 'lodash/debounce';
import axios from 'axios'

export default {
    name: 'InventoryListSection',

    components: {
        SearchField,
    },

    data() {
        return {
            itemsPerPage: 10,
            currentPage: 1,
            id: 1,
            loading: true,
            totalItems: 0,
            showForm: false,
            editingProduct: null,
            editingProductIndex: -1,
            searchQuery: '',
            products: [],
            headers: [
                { title: '#', value: 'index' },
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

        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },

        visiblePageRange() {
            const maxVisiblePages = 5;
            const halfMaxVisiblePages = Math.floor(maxVisiblePages / 2);
            const firstPage = Math.max(1, this.currentPage - halfMaxVisiblePages);
            const lastPage = Math.min(this.totalPages, firstPage + maxVisiblePages - 1);

            const range = [];
            for (let i = firstPage; i <= lastPage; i++) {
                range.push(i);
            }

            return range;
        },
    },

    async mounted() {
        await this.debouncedGetProducts();
    },

    methods: {
        debouncedGetProducts: _debounce(function () {
            this.getProducts();
        }, 1000),

        handleSearch(query) {
            this.searchQuery = query;
            this.currentPage = 1;
            this.debouncedGetProducts();
        },

        getProducts() {
            this.loading = true;
            axios
                .get('/products', {
                    params: {
                        page: this.currentPage,
                        itemsPerPage: this.itemsPerPage,
                        search: this.searchQuery,
                    }
                }).then((res) => {
                    this.products = res.data.products;
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                })
                .catch((error) => {
                    console.error('Error fetching products:', error);
                });
        },

        previousPage() {
            this.loading = true;
            if (this.currentPage > 1) {
                this.currentPage--;
                this.debouncedGetProducts();
            }
        },

        nextPage() {
            this.loading = true;
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.debouncedGetProducts();
            }
        },

        gotoPage(pageNumber) {
            this.loading = true;
            this.currentPage = pageNumber;
            this.debouncedGetProducts();
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
  