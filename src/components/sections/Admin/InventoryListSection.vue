<template>
    <v-container class="mt-5 section2">
        <v-row justify="center">
            <v-col cols="12">
                <h2 class="text-center">INVENTORY LIST</h2>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <CustomTable :columns="tableColumns" :items="products" height="500px"/>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
import SearchField from '../../common/SearchField.vue';
import CustomTable from '../../common/CustomTable.vue';

export default {
    mixins: [CustomTable],
    name: 'InventoryListSection',

    components: {
        SearchField,
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
                { key: 'stockOnHand', label: 'Stock On Hand' },
            ],

            products: [
                {productCode: 'P001', barCode: 1234567, description: 'description 1', brand: 'brand 1', category: 'category 1', price: 34, reorderLevel: 10, stockOnHand: 150}
            ],
        };
    },
};
</script>
  
<style scoped>
/* Add any scoped styles here */
</style>
  