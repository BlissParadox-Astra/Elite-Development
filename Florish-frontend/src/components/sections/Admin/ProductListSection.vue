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
                <CustomTable :columns="tableColumns" :items="products" :showEditIcon="true" :showDeleteIcon="true"
                    @edit-data="editProductRow" @delete-data="deleteProductRow" height="500px" />
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="10" xl="10" lg="10" md="10" class="form-container">
                        <ProductForm v-if="showForm" @add="addProduct" @update="updateProduct(editingProductIndex, $event)"
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
import axios from 'axios';

export default {
    mixins: [CustomTable],
    name: 'InventorySection',

    components: {
        SearchField,
        ProductForm,
        CustomTable,
    },

    data() {
        return {
            showForm: false,
            editingProduct: null,
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
            ],
        };
    },

    mounted() {
        this.getProducts();
    },

    methods: {
        getProducts() {
            axios.get('/products').then(res => {
                this.products = res.data.products
                console.log(this.products)
            });
        },

        showProductForm() {
            this.showForm = true;
        },

        hideProductForm() {
            this.showForm = false;
            this.editingProduct = null;
            this.editingProductIndex = -1;
        },

        addProduct(productData) {
            console.log('Adding product:', productData);
            this.products.push(productData);
            this.hideProductForm();
        },

        updateProduct(index, updatedProduct) {
            this.products[index] = updatedProduct;
            this.editingProduct = null;
            this.hideProductForm();
        },

        editProductRow(product) {
            this.editingProduct = { ...product };
            const index = this.products.findIndex(p => p.product_code === product.product_code);
            this.editingProductIndex = index;
            this.showForm = true;
        },

        deleteProductRow(product) {
            const index = this.products.findIndex(p => p.productCode === product.productCode);
            if (index !== -1) {
                this.products.splice(index, 1);
            }
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
    /* Adjust the maximum height as needed */
    overflow-y: auto;
}
</style>
  