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
                    @edit-data="editProductRow" @delete-data="deleteProductRow" height="500px"/>
            </v-col>
        </v-row>
        <v-row justify="center" v-if="showForm">
            <v-col cols="12" sm="6">
                <ProductForm @add="addProduct" @update="updateProduct(editingProductIndex, $event)"
                    @cancel="hideProductForm" :initialProduct="editingProduct" />
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '../../common/SearchField.vue';
import ProductForm from '../../common/ProductForm.vue';
import CustomTable from '../../common/CustomTable.vue';

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

            tableColumns: [
                { key: 'productCode', label: 'Product Code' },
                { key: 'barCode', label: 'Barcode' },
                { key: 'description', label: 'Description' },
                { key: 'brand', label: 'Brand' },
                { key: 'category', label: 'Category' },
                { key: 'price', label: 'Price' },
                { key: 'reorderLevel', label: 'Reorder Level' },
            ],

            products: [
                {productCode: 'P001', barCode: 1234567, description: 'description 1', brand: 'brand 1', category: 'category 1', price: 34, reorderLevel: 10}
            ],
        };
    },

    methods: {
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
            const index = this.products.findIndex(p => p.productCode === product.productCode);
            this.editingProductIndex = index;
            this.showForm = true;
        },
        
        deleteProductRow(product) {
            const index = this.products.findIndex(p => p.productCode === product.productCode);
            if (index !== -1) {
                this.products.splice(index, 1);
            }
        },
    },
};
</script>
  
<style scoped>
/* Add any scoped styles here */
</style>
  