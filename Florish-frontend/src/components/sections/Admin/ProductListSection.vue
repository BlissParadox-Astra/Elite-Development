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
                <CustomTable :columns="tableColumns" :items="paginatedProducts" :showEditIcon="true" :showDeleteIcon="true"
                    @edit-data="editProductRow" @delete-data="showDeleteConfirmation" height="500" />
                <v-pagination v-model="currentPage" :length="totalPages" />
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
import SearchField from '../../common/SearchField.vue';
import ProductForm from '../../common/ProductForm.vue';
import CustomTable from '../../common/CustomTable.vue';
import DeleteConfirmationDialog from '../../common/DeleteConfirmationDialog.vue';
import axios from 'axios';

export default {
    mixins: [CustomTable],
    name: 'InventorySection',

    components: {
        SearchField,
        ProductForm,
        CustomTable,
        DeleteConfirmationDialog,
    },

    data() {
        return {
            currentPage: 1,
            itemsPerPage: 10,
            showForm: false,
            editingProduct: null,
            editingProductIndex: -1,
            products: [],
            loadingCategories: false,
            loadingBrands: false,
            tableColumns: [
                { key: 'product_code', label: 'Product Code' },
                { key: 'barcode', label: 'Barcode' },
                { key: 'description', label: 'Description' },
                { key: 'brand', label: 'Brand', render: this.renderProductBrand },
                { key: 'category', label: 'Category', render: this.renderProductCategory },
                { key: 'price', label: 'Price' },
                { key: 'reorder_level', label: 'Reorder Level' },
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
            axios.get('/products').then(res => {
                this.products = res.data.products
                console.log(this.products)
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
</style>
  