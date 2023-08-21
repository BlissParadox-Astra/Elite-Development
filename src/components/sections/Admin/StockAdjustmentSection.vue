<template>
    <v-main>
        <v-container class="mt-5">
            <v-row justify="center">
                <v-col>
                    <h2 class="text-center">Stock Adjustment</h2>
                </v-col>
            </v-row>
            <SearchField />
            <CustomTable :columns="tableColumns" :items="products" :showFetchIcon="true" @fetch-data="fetchProduct" height="480px"/>
        </v-container>
        <v-container>
            <v-row>
                <v-col>
                    <v-text-field label="Reference No." placeholder="REF NO." readonly />
                </v-col>
                <v-col>
                    <v-select v-model="Options" :items="commandOptions"></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="6">
                    <v-text-field v-model="selectedRow.productCode" label="Product Code" placeholder="PRODUCT CODE" readonly />
                </v-col>
                <v-col cols="6">
                    <v-text-field v-model="selectedRow.barCode" label="Bar Code" placeholder="BAR CODE" readonly />
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <v-text-field v-model="selectedRow.description" label="Description" placeholder="DESCRIPTION" readonly/>
                </v-col>
                <v-col>
                    <v-text-field label="Remarks" placeholder="Enter REMARKS" />
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <v-text-field label="Quantity" placeholder="Enter QUANTITY" />
                </v-col>
                <v-col>
                    <v-text-field label="User" placeholder="USER" />
                </v-col>
            </v-row>
            <v-row class="mt-3">
                <v-col cols="2" offset-md="10">
                    <v-btn color="success" style="width: 150px;">Save</v-btn>
                </v-col>
            </v-row>
        </v-container>

    </v-main>
</template>
  
<script>
import SearchField from "../../common/SearchField.vue";
import CustomTable from "../../common/CustomTable.vue";

export default {
    name: "StockAdjustment",
    components: {
        SearchField,
        CustomTable,
    },
    data() {
        return {
            selectedRow: {
                productCode: "",
                barCode: "",
                description: "",
            },
            tableColumns: [
                { key: "productCode", label: "Product Code" },
                { key: 'barCode', label: 'Barcode' },
                { key: "description", label: "Description" },
                { key: 'price', label: 'Price' },
                { key: 'brand', label: 'Brand' },
                { key: 'category', label: 'Category' },
                { key: 'stockOnHand', label: 'Stock On Hand' },
            ],
            products: [
                { productCode: "P001", barCode: "123456789", description: "Product 1", price: "20", brand: "CocaCola", category: "Category", stockOnHand: "100", },
                { productCode: "P002", barCode: "987654321", description: "Product 2", price: "16", brand: "CocaCola", category: "Category", stockOnHand: "200", },
            ],
            commandOptions: [
                'Romove From Inventory',
                'Add to Inventory',
            ],
            Options: 'Select an Option',
        };
    },
    methods: {
        fetchProduct(product) {
            this.selectedRow.referenceNo = product.referenceNo;
            this.selectedRow.productCode = product.productCode;
            this.selectedRow.barCode = product.barCode;
            this.selectedRow.description = product.description;
        },
    },
};
</script>
  