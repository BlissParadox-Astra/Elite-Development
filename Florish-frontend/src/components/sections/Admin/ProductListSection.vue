<template>
    <v-container class="mt-5 section2">
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
            <v-col cols="12" sm="3">
                <v-btn color="success" block @click="showProductForm">Add New Product</v-btn>
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
                            <td>
                                <span>
                                    <v-icon @click="editProductRow(item)" color="primary">mdi-pencil</v-icon>
                                </span>
                                <span style="margin-left: 2px;">
                                    <v-icon @click="showDeleteConfirmation(item)" color="error">mdi-delete</v-icon>
                                </span>
                            </td>
                        </tr>
                    </template>
                </v-data-table-server>
            </v-col>
        </v-row>
        <DeleteConfirmationDialog @confirm-delete="deleteProduct" ref="deleteConfirmationDialog" />
        <v-row>
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="10" xl="10" lg="10" md="10" class="form-container">
                        <ProductForm v-if="showForm" @add="addProduct" @update="updateProduct(editingProductIndex, $event)"
                            :existingCategories="existingCategories" :existingBrands="existingBrands"
                            @cancel="hideProductForm" :initialProduct="editingProduct" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '../../commons/SearchField.vue';
import ProductForm from '../../forms/ProductForm.vue';
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
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
            itemsPerPage: 10,
            page: 1,
            id: 1,
            showForm: false,
            editingProduct: null,
            editingProductIndex: -1,
            products: [],
            totalItems: 0,
            loading: true,
            loadingCategories: false,
            loadingBrands: false,
            headers: [
                { title: '#', key: 'id' },
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

    async mounted() {
        this.loadingCategories = true;
        await this.fetchCategories();
        this.loadingCategories = false;
        this.loadingBrands = true;
        await this.fetchBrands();
        this.loadingBrands = false;
        await this.getProducts();
    },

    methods: {
        getProducts() {
            this.loading = true;
            axios
                .get('/products', {
                    params: {
                        page: this.page,
                        itemsPerPage: this.itemsPerPage,
                    }
                })
                .then((res) => {
                    this.products = [...res.data.products.data];
                    this.totalItems = res.data.totalItems;
                    this.loading = false;
                })
                .catch((error) => {
                    console.error('Error fetching products:', error);
                });
        },

        async fetchCategories() {
            try {
                const response = await axios.get('/categories');
                this.existingCategories = response.data.categories;
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        },

        async fetchBrands() {
            try {
                const response = await axios.get('/brands');
                this.existingBrands = response.data.brands;
            } catch (error) {
                console.error('Error fetching brands:', error);
            }
        },

        addProduct(productData) {
            console.log('Adding product:', productData);
            this.products.push(productData);
            this.hideProductForm();
        },

        editProductRow(product) {
            const category = this.existingCategories.find(category => category.category_name === product.category.category_name);
            const brand = this.existingBrands.find(brand => brand.brand_name === product.brand.brand_name);

            if (category && brand) {
                this.editingProduct = {
                    ...product,
                    category_name: product.category.category_name,
                    brand_name: product.brand.brand_name,
                };

                const index = this.products.findIndex(p => p.product_code === product.product_code);
                this.editingProductIndex = index;
                this.showForm = true;
            } else {
                console.error(`Category "${product.category.category_name}" or brand "${product.brand.brand_name}" not found.`);
            }
        },

        updateProduct(index, updatedProduct) {
            this.products[index] = updatedProduct;
            this.editingProduct = null;
            this.hideProductForm();
        },

        deleteProduct() {
            if (this.itemToDelete) {
                axios.delete(`/product/${this.itemToDelete.id}`)
                    .then(() => {
                        const index = this.products.findIndex(product => product.id === this.itemToDelete.id);
                        if (index !== -1) {
                            this.products.splice(index, 1);
                        }
                        this.$refs.deleteConfirmationDialog.closeDialog();
                    })
                    .catch(error => {
                        console.error('Error deleting item:', error);
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
        },

        renderProductCategory(category) {
            return category.category ? category.category.category_name : 'Unknown';
        },

        renderProductBrand(brand) {
            return brand.brand ? brand.brand.brand_name : 'Unknown';
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

.custom-table {
    height: 445px;
}
</style>
  