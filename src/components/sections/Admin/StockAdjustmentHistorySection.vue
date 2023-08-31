<template>
    <v-main>
        <v-container class="mt-5">
            <v-row justify="center">
                <v-col>
                    <h2 class="text-center">Stock Adjustment History</h2>
                </v-col>
            </v-row>
            <v-row>
            <v-col cols="12" sm="9">
                <SearchField />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <CustomTable :columns="tableColumns" :items="products" height="480px" />
            </v-col>
        </v-row>
        </v-container>
    </v-main>
</template>
  
<script>
import SearchField from "../../common/SearchField.vue";
import CustomTable from "../../common/CustomTable.vue";

export default {
    name: "StockAdjustmentHistory",
    components: {
        SearchField,
        CustomTable,
    },
    data() {
        return {
            selectedRow: {
                referenceNo: "",
                action: "",
                productCode: "",
                barCode: "",
                description: "",
                remarks: "",
                quantity: "",
                user: "",
            },

            tableColumns: [
                { key: "referenceNo", label: "Reference No." },
                { key: "action", label: "Action" },
                { key: "productCode", label: "Product Code" },
                { key: 'barCode', label: 'Barcode' },
                { key: "description", label: "Description" },
                { key: "remarks", label: "Remarks" },
                { key: 'quantity', label: 'Quantity' },
                { key: 'user', label: 'User' },
            ],

            products: [
                { referenceNo: "2023828", action: "remove from inventory", productCode: "BV-0001", barCode: "123456789", description: "Product 1", remarks: "damage", quantity: "2", user: "John Doe"},
            ],
        };
    },
};
</script>
  