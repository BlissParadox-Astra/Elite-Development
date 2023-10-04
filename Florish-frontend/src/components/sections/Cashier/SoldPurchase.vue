<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12">
                        <h2 class="text-center">SOLD PURCHASE</h2>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" sm="12" lg="9" md="12">
                        <FilterByDate></FilterByDate>
                    </v-col>
                    <v-col cols="12" xl="5" lg="3" class="d-flex align-center">
                        <v-card class="pa-3 total-card">
                            <v-row class="text-left">
                                <v-col v-for="(column, index) in tableColumns" :key="index" cols="3">
                                    <span class="total-value">{{ calculateTotal(column.total) }}</span>
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <CustomTable :columns="tableColumns" :items="products" :isStockEntryPage="true" :showFetchIcon="true"
                    @delete-data="deleteProductRow" @edit-quantity="openEditQuantityDialog" @fetch-data="fetchData" height="450px" />
            </v-col>
        </v-row>
        <v-row v-if="showFetchForm">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="8" md="8" lg="8" xl="10" class="form-container">
                        <CancelOrderDetails @close="closeCancelOrderForm" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-row class="d-flex justify-space-between">
            <v-col cols="12" sm="6" lg="3" class="text-start">
                <v-btn to="/cashierdashboard" color="success" block>BACK</v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import CustomTable from "../../common/CustomTable.vue";
import FilterByDate from "@/components/common/FilterByDate.vue";
import CancelOrderDetails from "@/components/common/CancelOrderForm.vue"
export default {
    name: 'transactionCart',
    components: {
        CustomTable,
        FilterByDate,
        CancelOrderDetails,
    },
    data() {
        return {
            showFetchForm: false,
            tableColumns: [
                { key: "invoiceNo", label: "Invoice No." },
                { key: "productCode", label: "Product Code" },
                { key: "barcode", label: "Bar Code" },
                { key: "description", label: "Description" },
                { key: "price", label: "Price" },
                { key: "quantity", label: "Quantity" },
                { key: "total", label: "Total" },
                { key: "transacBy", label: "Transaction By" },
            ],

            products: [
                { invoiceNo: "Invoice001", productCode: "P001", barcode: "123456789", description: "Product 1", price: 30, quantity: "5", total: "800", transacBy: "cashier", },
                { invoiceNo: "Invoice002", productCode: "P002", barcode: "987654321", description: "Product 2", price: 400, quantity: "6", total: "1000", transacBy: "cashier", },
            ],

        };

    },
    methods: {
        calculateTotal(total) {
            return total;
        },
        fetchData() {
            // Show the FetchForm when the Fetch icon is clicked
            this.showFetchForm = true;
        },
        closeCancelOrderForm() {
            this.showFetchForm = false;
        },
    }
}
</script>
