<template>
    <v-container class="mt-5 section2">
        <v-row justify="center">
            <v-col cols="12">
                <h2 class="text-center">CANCELLED ORDER LIST</h2>
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
    name: 'CancelOrderSection',
    components: {
        SearchField,
        CustomTable,
    },
    data() {
        return {
            showForm: false,
            products: [],
            editingProduct: null,
            editingProductIndex: -1,
            tableColumns: [
                { key: 'productCode', label: 'Product Code' },
                { key: 'barCode', label: 'Barcode' },
                { key: 'description', label: 'Description' },
                { key: 'price', label: 'Price' },
                { key: 'quantity', label: 'Quantity' },
                { key: 'total', label: 'Total' },
                { key: 'date', label: 'Date' },
                { key: 'canceledBy', label: 'Canceled By' },
                { key: 'reason', label: 'Reason' },
                { key: 'action', label: 'Action' },
            ],
        };
    },
};
</script>
  
<style scoped>
/* Add any scoped styles here */
</style>
  