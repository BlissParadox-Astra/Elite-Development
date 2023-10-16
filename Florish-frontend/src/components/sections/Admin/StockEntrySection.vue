<template>
    <v-main class="section2">
        <v-container>
            <v-row class="mt-3">
                <v-col cols="12" sm="5" md="5" lg="3" xl="5">
                    <v-text-field label="Reference Number" v-model="reference_number" readonly />
                </v-col>
                <v-col cols="12" sm="2" class="d-flex justify-center align-center">
                    <v-btn color="success" block @click="generateAndFetchReferenceNumber" :disabled="isGeneratingReferenceNumber">Generate</v-btn>
                </v-col>
                <v-col cols="12" sm="5" md="5" lg="3" xl="5">
                    <v-text-field label="Stock In Date" type="date" v-model="stock_in_date" />
                </v-col>
                <v-col cols="12" sm="2" class="d-flex justify-center align-center">
                    <v-btn color="success" block @click="showBrowseProductForm" :disabled="!canBrowseProduct">Browse
                        Product</v-btn>
                </v-col>
                <v-col cols="12" sm="2" md="2" lg="2" xl="2">
                    <v-text-field label="Stock In By" readonly :model-value="stock_in_by" />
                </v-col>
            </v-row>
            <v-row v-if="showBrowseProduct">
                <v-col cols="12">
                    <v-row class="d-flex justify-center">
                        <v-col cols="12" sm="10" md="10" lg="10" xl="10" class="form-container">
                            <BrowseProduct @close="closeBrowseProductForm" :addToCart="addToCartProduct" />
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <CustomTable :columns="tableColumns" :items="products" :showDeleteIcon="true" :isStockEntryPage="true"
                @delete-data="deleteProductRow" @edit-quantity="openEditQuantityDialog"
                @add-to-cart-product="addToCartProduct" :reference_number="reference_number" :stock_in_date="stock_in_date"
                :stock_in_by="stock_in_by" height="450px" />
            <v-row class="mt-5 save-btn">
                <v-col cols="2" offset-md="10">
                    <v-btn color="success" @click="showConfirmation" style="width: 150px;" :disabled="isSaveButtonDisabled">Save</v-btn>
                </v-col>
            </v-row>
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
                            <v-btn color="success" @click="saveRecord" style="width: 150px;">Save</v-btn>
                            <v-btn @click="cancelSave">Cancel</v-btn>
                        </div>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="showEditQuantityDialog" max-width="400" class="center-dialog  no-background">
                <v-card>
                    <v-card-title>Edit Quantity</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="editedQuantity" label="New Quantity"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="d-flex justify-center">
                        <v-btn color="primary" @click="saveEditedQuantity" :disabled="isEditQuantitySaveButtonDisabled">Save</v-btn>
                        <v-btn @click="closeEditQuantityDialog">Cancel</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </v-main>
</template>

<script>
import CustomTable from '../../common/CustomTable.vue';
import BrowseProduct from '../../common/BrowseProduct.vue';
import { VIcon } from "vuetify/lib/components";
import { mapState } from 'vuex';
import axios from 'axios';
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
            isGeneratingReferenceNumber: false,
            products: [],
            editedQuantity: 0,
            editingIndex: -1,
            reference_number: '',
            stock_in_date: '',
            tableColumns: [
                { key: "reference_number", label: "Reference No." },
                { key: 'product_code', label: 'Product Code' },
                { key: 'barcode', label: 'Bar Code' },
                { key: "description", label: "Description" },
                { key: "quantity_added", label: "Quantity" },
                { key: "stock_in_date", label: "Stock In Date" },
                { key: "stock_in_by", label: "Stock In By" },
            ],
        };
    },
    computed: {
        ...mapState({
            user: state => state.user,
        }),
        stock_in_by() {
            if (this.user && this.user.first_name && this.user.last_name) {
                return `${this.user.first_name} ${this.user.last_name}`;
            }
            return '';
        },
        canBrowseProduct() {
            return this.reference_number && this.stock_in_date && this.stock_in_by;
        },
        isEditQuantitySaveButtonDisabled() {
            return this.editedQuantity === '' || isNaN(this.editedQuantity);
        },
        isSaveButtonDisabled() {
            return this.reference_number === '' || this.stock_in_date === '' || this.stock_in_by === '' || this.products.length === 0;
        }
    },
    created() {
        const queryDate = this.$route.query.date;
        if (queryDate) {
            this.stock_in_date = queryDate;
        }
    },
    methods: {
        showBrowseProductForm() {
            this.showBrowseProduct = true;
        },
        closeBrowseProductForm() {
            this.showBrowseProduct = false;
        },
        addToCartProduct(product) {
            const reference_number = this.reference_number;
            const stock_in_date = this.stock_in_date;
            const stock_in_by = this.stock_in_by;

            const product_id = product.product_id;

            const existingProduct = this.products.find(p => p.product_code === product.product_code);
            if (existingProduct !== undefined) {
                existingProduct.quantity_added++;
            } else {
                const newProduct = {
                    product_code: product.product_code,
                    barcode: product.barcode,
                    quantity_added: 1,
                    description: product.description,
                    reference_number,
                    stock_in_date,
                    stock_in_by,
                    product_id,
                };
                this.products.push(newProduct);
            }
        },
        deleteProductRow(product) {
            const index = this.products.findIndex(p => p.productCode === product.productCode);
            if (index !== -1) {
                this.products.splice(index, 1);
            }
        },
        openEditQuantityDialog(index) {
            this.editingIndex = index;
            this.editedQuantity = this.products[index].quantity_added;
            this.showEditQuantityDialog = true;
        },
        saveEditedQuantity() {
            if (this.editingIndex !== -1) {
                this.products[this.editingIndex].quantity_added = this.editedQuantity;
                this.showEditQuantityDialog = false;
                this.editingIndex = -1;
                this.editedQuantity = 0;
            }
        },
        closeEditQuantityDialog() {
            this.showEditQuantityDialog = false;
            this.editingIndex = -1;
            this.editedQuantity = 0;
        },
        showConfirmation() {
            this.showConfirmationDialog = true;
        },
        saveRecord() {
            this.showConfirmationDialog = false;
            const stockInRequests = this.products.map((product) => {
                return {
                    reference_number: this.reference_number,
                    stock_in_date: this.stock_in_date,
                    stock_in_by: this.stock_in_by,
                    product_id: product.product_id,
                    quantity_added: product.quantity_added,
                };
            });

            const stockInData = {
                stock_in_requests: stockInRequests,
            };

            axios
                .post("/stockIn", stockInData)
                .then(() => {
                    console.log("Stock-In records saved successfully");
                    this.resetData();
                    this.isGeneratingReferenceNumber = false;
                })
                .catch((error) => {
                    console.error("Error saving Stock-In records", error);
                    this.isGeneratingReferenceNumber = true;
                });
        },
        cancelSave() {
            this.showConfirmationDialog = false;
        },
        generateAndFetchReferenceNumber() {
            this.isGeneratingReferenceNumber = true;
            axios.get('/stockIn/generate-reference-number')
                .then(response => {
                    this.reference_number = response.data.reference_number;
                })
                .catch(error => {
                    console.error('Error fetching reference number', error);
                });
        },
        resetData() {
            this.products = [];
            this.reference_number = '';
        }
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
}
</style>
