<template>
    <v-main class="section2">
        <v-container>
            <!-- Reference Number Field and Stock In By Field -->
            <v-row class="mt-3">
                <v-col cols="12" sm="5" md="5" lg="3" xl="5">
                    <v-text-field label="Reference Number" placeholder="Enter Reference Number" readonly />
                </v-col>
                <v-col cols="12" sm="2" class="d-flex justify-center align-center">
                    <v-btn color="success" block>Generate</v-btn>
                </v-col>
                <v-col cols="12" sm="5" md="5" lg="3" xl="5">
                    <v-text-field label="Stock In Date" type="date" />
                </v-col>
                <v-col cols="12" sm="2" class="d-flex justify-center align-center">
                    <v-btn color="success" block @click="showBrowseProductForm">Browse Product</v-btn>
                </v-col>
                <v-col cols="12" sm="2" md="2" lg="2" xl="2">
                    <v-text-field label="Stock In By" placeholder="Enter Stock In By" readonly />
                </v-col>
            </v-row>
            <!-- Browse Product Component -->
            <v-row v-if="showBrowseProduct">
                <v-col cols="12">
                    <v-row class="d-flex justify-center">
                        <v-col cols="12" sm="10" md="10" lg="10" xl="10" class="form-container">
                            <BrowseProduct  @close="closeBrowseProductForm" @add-to-cart-product="addToCartProduct" />
                        </v-col> 
                    </v-row>
                </v-col>
            </v-row>
            <!-- <ProductTable :columns="tableColumns" :items="products" :showDeleteIcon="true" :isStockEntryPage="true"
                @delete-data="deleteProductRow" @edit-quantity="openEditQuantityDialog" /> -->
            <CustomTable :columns="tableColumns" :items="products" :showDeleteIcon="true" :isStockEntryPage="true"
                @delete-data="deleteProductRow" @edit-quantity="openEditQuantityDialog" height="450px" />
            <v-row class="mt-5 save-btn">
                <v-col cols="2" offset-md="10">
                    <v-btn color="success" @click="showConfirmation" style="width: 150px;">Save</v-btn>
                </v-col>
            </v-row>
            <!-- For save button warning modal -->
            <v-dialog v-model="showConfirmationDialog" max-width="400" class="center-dialog  no-background">
                <v-card>
                    <v-card-title>
                        <v-icon left>mdi-alert-circle-outline</v-icon>
                        Confirm Save
                    </v-card-title>
                    <v-card-text class="text-center">
                        <v-icon left>mdi-comment-question</v-icon>
                        ARE YOU SURE YOU WANT TO SAVE THIS RECORDS?
                    </v-card-text>
                    <v-card-actions class="d-flex justify-center">
                        <div>
                            <v-btn color="success" @click="saveRecord">Save</v-btn>
                            <v-btn @click="cancelSave">Cancel</v-btn>
                        </div>
                    </v-card-actions>
                </v-card>
            </v-dialog>
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
        </v-container>
    </v-main>
</template>

  
<script>
import CustomTable from '../../commons/CustomTable.vue';
import BrowseProduct from '../../commons/BrowseProduct.vue';
import { VIcon } from "vuetify/lib/components";
export default {
    components: {
        CustomTable,
        BrowseProduct,
        VIcon,
    },
    data() {
        return {
            showEditQuantityDialog: false,
            showConfirmationDialog: false,
            showBrowseProduct: false,
            products: [],
            editedQuantity: 0,
            editingIndex: -1,
            tableColumns: [
                { key: "referenceNo", label: "Reference No." },
                { key: 'productCode', label: 'Product Code' },
                { key: 'barCode', label: 'Bar Code' },
                { key: "description", label: "Description" },
                { key: "quantity", label: "Quantity" },
                { key: "stockInDate", label: "Stock In Date" },
                { key: "stockInBy", label: "Stock In By" },
            ],

        };
    },
    methods: {
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
        showConfirmation() {
            this.showConfirmationDialog = true;
        },
        saveRecord() {
            this.showConfirmationDialog = false;
        },
        cancelSave() {
            this.showConfirmationDialog = false;
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

}

.center-dialog {
    position: fixed;
    left: 30%;
    right: 15%;
    /* transform: translate(-50%, -50%); */
}
</style>
  