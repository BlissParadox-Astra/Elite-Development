<template>
    <v-container class="mt-2 showBrowseProductForm">
        <h2 class="text-center mb-4">Product Module</h2>
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-btn icon @click="closeForm" class="close-icon">
            <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-row justify="center">
            <v-col cols="12">
                <CustomTable :columns="tableColumns" :items="products" :showAddToCartIcon="true"
                    @add-to-cart-product="addToCartProduct" height="420px"/>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '@/components/common/SearchField.vue';
import CustomTable from './CustomTable.vue';
export default {
    components: {
        SearchField,
        CustomTable,
    },
    data() {
        return {
            tableColumns: [
                { key: 'productCode', label: 'Product Code' },
                { key: 'barCode', label: 'Barcode' },
                { key: 'description', label: 'Description' },
                { key: 'stockOnHand', label: 'Stock On Hand' },
            ],
            products: [
                { productCode: "P001", barCode: "123456789", description: "Product 1", stockOnHand: "100", },
                { productCode: "P002", barCode: "987654321", description: "Product 2", stockOnHand: "200", },
            ],
            showProductForm: false,
        };
    },
    methods: {
        closeForm() {
            this.showProductForm = false;
            this.$emit('close');
        },
        addToCartProduct(product) {
            this.$emit('add-to-cart-product', product);
        },
    },
};
</script>

<style>
.showBrowseProductForm {
    position: fixed;
    top: 50%;
    left: 58%;
    height: 81%;
    transform: translate(-50%, -50%);
    background-color: rgba(114, 165, 104, 0.9);
    z-index: 999;
    padding: 10px;
}

.close-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #fff;
}
</style>
