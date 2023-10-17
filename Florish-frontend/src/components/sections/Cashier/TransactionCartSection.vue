<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12">
                        <h2 class="text-center">TRANSACTION CART</h2>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="12" sm="6" lg="2" class="d-flex align-center">
                        <v-label class="mr-3">TRANSACTION ID:</v-label> <!-- Added margin-right -->
                    </v-col>
                    <v-col cols="12" sm="6" lg="3">
                        <v-text-field class="ml-n6" label="Transaction ID" variant="plain"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" lg="2" class="d-flex align-center w-auto">
                        <v-label class="mr-n7">TRANSACTION DATE:</v-label>
                    </v-col>
                    <v-col cols="12" sm="6" lg="4" >
                        <v-text-field class="ml-n2" label="Transaction Date" variant="plain"></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" sm="6" lg="2" class="d-flex align-center">
                        <v-label >BARCODE:</v-label>
                    </v-col>
                    <v-col cols="12" sm="6" lg="3">
                       <SearchField class="ml-n6"></SearchField>
                    </v-col>
                    <v-col cols="12" sm="6" lg="3" class="d-flex align-center">
                        <h5 class="text-blue"  @click="showBrowseProductForm" >[CLICK HERE TO BROWSE PRODUCT] </h5>
                        <!-- <v-btn color="success" block>Browse Product</v-btn> -->
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
                <CustomTable :columns="tableColumns" :items="products" :showMinusIcon="true" :showPlusCartIcon="true"  :isStockEntryPage="true"
                    @delete-data="deleteProductRow" :showDeleteIcon="true"  @edit-quantity="openEditQuantityDialog" height="450px" />
            </v-col>
        </v-row>
        <!--Browse Product -->
        <v-row v-if="showBrowseProduct">
            <v-col cols="12">
                <v-row class="d-flex justify-center">
                    <v-col cols="12" sm="8" md="8" lg="8" xl="10" class="form-container">
                        <BrowseProduct @close="closeBrowseProductForm" @add-to-cart-product="addToCartProduct" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <!-- Edit Quantity Dialog -->
        <v-dialog v-model="showEditQuantityDialog" max-width="400" class="center-dialog  no-background">
            <v-card>
                <v-card-title>Edit Quantity</v-card-title>
                <v-card-text>
                    <v-text-field v-model="editedQuantity" label="New Quantity"></v-text-field>
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <v-btn color="primary" @click="saveEditedQuantity">Save</v-btn>
                    <v-btn @click="closeEditQuantityDialog">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-row class="d-flex justify-space-between">
            <v-col cols="12" sm="6" lg="3" class="text-start">
                <v-btn to="/cashier-dashboard" color="success" block>BACK</v-btn>
            </v-col>
            <v-col cols="12" sm="6" lg="3" class="text-end">
                <v-btn color="success" block>SOLD</v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import CustomTable from "../../commons/CustomTable.vue";
import BrowseProduct from '../../commons/BrowseProduct.vue';
import SearchField from '../../commons/SearchField.vue';
export default {
    name: 'TransactionCart',
    components: {
        CustomTable,
        BrowseProduct,
        SearchField,
    },
    data() {
        return {
            showEditQuantityDialog: false,
            showBrowseProduct: false,
            editedQuantity: 0,
            editingIndex: -1,
            tableColumns: [
                { key: "PCode", label: "PCode" },
                { key: "BarCode", label: "BarCode" },
                { key: "description", label: "Description" },
                { key: "price", label: "Price" },
                { key: "quantity", label: "Quantity" },
                { key: "total", label: "Total" },
            ],

            products: [
                {  PCode: "P001", BarCode: "123456789", description: "Product 1", price: 30, quantity: "5", total: "800",  },
                {  PCode: "P002", BarCode: "987654321", description: "Product 2", price: 400, quantity: "6", total: "1000", },
            ],
        };
    },
    methods: {
        calculateTotal(total) {
            return total;
        },
        showBrowseProductForm() {
            this.showBrowseProduct = true;
        },
        closeBrowseProductForm() {
            this.showBrowseProduct = false;
        },
        addToCartProduct(product) {
            console.log("Adding to cart:", product);
            this.products.push;
        },
        deleteProductRow(product) {
            const index = this.products.findIndex(p => p.productCode === product.productCode);
            if (index !== -1) {
                this.products.splice(index, 1);
            }
        },
        openEditQuantityDialog(index) {
            // Set editingIndex and editedQuantity based on the clicked row
            this.editingIndex = index;
            this.editedQuantity = this.products[index].quantity;
            this.showEditQuantityDialog = true; // Open the dialog
        },
        saveEditedQuantity() {
            if (this.editingIndex !== -1) {
                this.products[this.editingIndex].quantity = this.editedQuantity;
                this.showEditQuantityDialog = false;
                this.editingIndex = -1;
                this.editedQuantity = 0;
            }
        },
        closeEditQuantityDialog() {
            this.showEditQuantityDialog = false;
            this.editingIndex = -1;
            this.editedQuantity = 0
                ;
        },
    },
}
</script>

<style scoped>
.total-card {

    background-color: #d8d3d3;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
    height: 30%;

}

.total-value {
    font-weight: bold;
    color: #333;
}

.form-container {
    position: absolute;
    top: 10%;
    left: 1;
    right: 1;
    z-index: 999;

}

.center-dialog {
    position: fixed;
    left: 30%;
    right: 15%;
    /* transform: translate(-50%, -50%); */
}
</style>