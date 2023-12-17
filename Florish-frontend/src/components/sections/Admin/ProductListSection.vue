<template>
    <v-container class="mt-14 section2">
        <v-row justify="space-between">
            <v-col cols="12" sm="9" md="5" lg="5">
                <SearchField @search="handleSearch" :searchType="'regular'" />
            </v-col>
            <v-col cols="12" sm="3">
                <v-btn color="#23b78d" block @click="showProductForm">Add Product</v-btn>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
                    @update:options="debouncedGetProducts" fixed-header height="450">
                    <template v-slot:custom-sort="{ header }">
                        <span v-if="header.key === 'actions'">Actions</span>
                    </template>
                    <template v-slot:item="{ item, index }">
                        <tr>
                            <td>{{ displayedIndex + index }}</td>
                            <td>{{ item.product_code }}</td>
                            <td>{{ item.barcode }}</td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.brand ? item.brand.brand_name : 'Unknown Brand' }}</td>
                            <td>{{ item.category ? item.category.category_name : 'Unknown Category' }}</td>
                            <td>₱{{ item.price }}</td>
                            <td>{{ item.reorder_level }}</td>
                            <td>
                                <span>
                                    <v-icon @click="editProductRow(item)" color="#23b78d">mdi-pencil</v-icon>
                                </span>
                                <span style="margin-left: 2px;">
                                    <v-icon @click="showDeleteConfirmation(item)" color="#23b78d">mdi-delete</v-icon>
                                </span>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:bottom>
                        <v-col cols="12">
                            <div v-if="totalPages > 1" class="text-center pt-5 pagination">
                                <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                                    :disabled="currentPage === 1">
                                    <v-icon>mdi-chevron-left</v-icon> Prev
                                </v-btn>

                                <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber"
                                    @click="gotoPage(pageNumber)" :class="{ active: pageNumber === currentPage }"
                                    class="pagination-button">
                                    {{ pageNumber }}
                                </v-btn>

                                <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                                    :disabled="currentPage === totalPages">
                                    Next <v-icon>mdi-chevron-right</v-icon>
                                </v-btn>
                            </div>
                        </v-col>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
        <DeleteConfirmationDialog @confirm-delete="deleteProduct" ref="deleteConfirmationDialog" />
        <v-row>
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="10" xl="10" lg="10" md="10" class="form-container">
                        <ProductForm v-if="showForm" @add-product="addProduct" @update-product="updateProduct"
                            :existingCategories="existingCategories" :existingBrands="existingBrands"
                            @cancel="hideProductForm" :initialProduct="editingProduct" />
                    </v-col>
                </v-row>
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
import SearchField from '../../commons/SearchField.vue';
import ProductForm from '../../forms/ProductForm.vue';
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
import _debounce from 'lodash/debounce';
import axios from 'axios';

export default {
    name: 'InventorySection',

    components: {
        SearchField,
        ProductForm,
        DeleteConfirmationDialog,
    },

    data() {
        return {
            currentPage: 1,
            itemsPerPage: 10,
            id: 1,
            showForm: false,
            editingProduct: null,
            editingProductIndex: -1,
            products: [],
            totalItems: 0,
            loading: true,
            loadingCategories: false,
            loadingBrands: false,
            existingBrands: [],
            existingCategories: [],
            snackbar: false,
            snackbarColor: '',
            searchQuery: '',
            headers: [
                { title: '#', value: 'index' },
                { title: 'Product Code', key: 'product_code' },
                { title: 'Barcode', key: 'barcode' },
                { title: 'Description', key: 'description' },
                { title: 'Brand', key: 'brand.brand_name' },
                { title: 'Category', key: 'category.category_name' },
                { title: 'Price', key: 'price' },
                { title: 'Reorder Level', key: 'reorder_level' },
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

        visiblePageRange() {
            const smallScreenMaxPages = 2;
            const largeScreenMaxPages = 5;

            const maxVisiblePages = this.isSmallScreen ? smallScreenMaxPages : largeScreenMaxPages;
            const halfMaxVisiblePages = Math.floor(maxVisiblePages / 2);
            const firstPage = Math.max(1, this.currentPage - halfMaxVisiblePages);
            const lastPage = Math.min(this.totalPages, firstPage + maxVisiblePages - 1);

            const range = [];
            for (let i = firstPage; i <= lastPage; i++) {
                range.push(i);
            }

            return range;
        },

        isSmallScreen() {
            return window.innerWidth < 768;
        },
    },

    async mounted() {
        this.loadingCategories = true;
        await this.fetchCategories();
        this.loadingCategories = false;
        this.loadingBrands = true;
        await this.fetchBrands();
        this.loadingBrands = false;
        await this.debouncedGetProducts();
        // this.$nextTick(() => {
        //     if (this.$refs.searchField && this.$refs.searchField.$refs.searchField) {
        //         this.$refs.searchField.$refs.searchField.focus();
        //     }
        // });
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
                })
                .then((res) => {
                    this.products = res.data.products;
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                })
                .catch((error) => {
                    console.error('Error fetching products:', error);
                });
        },

        async fetchCategories() {
            try {
                const response = await axios.get('/get-categories');
                this.existingCategories = response.data;
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        },

        async fetchBrands() {
            try {
                const response = await axios.get('/get-brands');
                this.existingBrands = response.data;
            } catch (error) {
                console.error('Error fetching brands:', error);
            }
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

        async addProduct(productData) {
            try {
                const response = await axios.post('/product', productData);
                if (response.status === 200) {
                    this.products.push(response.data);
                    this.hideProductForm();
                    this.snackbarColor = 'success';
                    this.showSnackbar(response.data.message, 'success');
                    this.getProducts();
                } else {
                    this.snackbarColor = 'error';
                    this.showSnackbar(response.data.message, 'error');
                }
            } catch (error) {
                console.error('Error adding product:', error);
                this.snackbarColor = 'error';
                this.showSnackbar(error.response.data.message, 'error');
            }
        },

        editProductRow(product) {
            const category = this.existingCategories.find(category => category.category_name === product.category.category_name);
            const brand = this.existingBrands.find(brand => brand.brand_name === product.brand.brand_name);

            if (category && brand) {
                this.editingProduct = {
                    ...product,
                    category_name: product.category.category_name,
                    brand_name: product.brand.brand_name,
                    id: product.id,
                };
                const index = this.products.findIndex(p => p.product_code === product.product_code);
                this.editingProductIndex = index;
                this.showForm = true;
            } else {
                this.editingProduct = {
                    ...product,
                    category_name: '',
                    brand_name: '',
                    id: product.id,
                };
                const index = this.products.findIndex(p => p.product_code === product.product_code);
                this.editingProductIndex = index;
                this.showForm = true;
                console.error(`Category "${product.category.category_name}" or brand "${product.brand.brand_name}" not found.`);
            }
        },


        async updateProduct(productData) {
            try {
                const response = await axios.put(`/product/${productData.id}`, productData);
                if (response.status === 200) {
                    this.products[this.editingProductIndex] = productData;
                    this.hideProductForm();
                    this.snackbarColor = 'success';
                    this.showSnackbar(response.data.message, 'success');
                    this.editingProductIndex = -1;
                    this.editingProduct = null;
                    this.getProducts();
                } else {
                    this.snackbarColor = 'error';
                    this.showSnackbar(response.data.message, 'error');
                }
            } catch (error) {
                console.error(error);
                if (error.response && error.response.status === 422) {
                    const validationErrors = error.response.data.errors;
                    const errorMessage = Object.values(validationErrors).flat()[0] || 'An error occurred';
                    this.snackbarColor = 'error';
                    this.showSnackbar(errorMessage, 'error');
                } else {
                    this.snackbarText = error.response.data.error;
                    this.snackbarColor = 'error';
                    this.showSnackbar(this.snackbarText, 'error');
                }
            }
        },

        async deleteProduct() {
            if (this.itemToDelete) {
                return axios
                    .delete(`/product/${this.itemToDelete.id}`)
                    .then((response) => {
                        const index = this.products.findIndex(product => product.id === this.itemToDelete.id);
                        if (index !== -1) {
                            this.products.splice(index, 1);
                            this.showSnackbar(response.data.message, 'success');
                        } else {
                            this.showSnackbar('Product not found in the list', 'error');
                        }
                    })
                    .catch((error) => {
                        console.error('Error deleting item:', error);
                        this.showSnackbar(error.response.data.message, 'error');
                    })
                    .finally(() => {
                        this.$refs.deleteConfirmationDialog.closeDialog();
                        this.getProducts();
                    });
            }
        },

        showDeleteConfirmation(item) {
            this.itemToDelete = item;
            this.$refs.deleteConfirmationDialog.showConfirmationDialog();
        },

        showProductForm() {
            this.showForm = true;
        },

        hideProductForm() {
            this.showForm = false;
            this.editingProduct = null;
            this.editingProductIndex = -1;
            this.getProducts();
        },

        renderProductCategory(category) {
            return category.category ? category.category.category_name : 'Unknown';
        },

        renderProductBrand(brand) {
            return brand.brand ? brand.brand.brand_name : 'Unknown';
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
    },
};
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
    border-color: #23b78d;
}
</style>
  