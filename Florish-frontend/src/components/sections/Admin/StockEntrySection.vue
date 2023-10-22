<template>
    <v-main class="section2">
        <v-container>
            <v-row class="mt-3">
                <v-col cols="12" sm="5" md="5" lg="3" xl="5">
                    <v-text-field label="Reference Number" v-model="reference_number" readonly />
                </v-col>
                <v-col cols="12" sm="2" class="d-flex justify-center align-center">
                    <v-btn color="success" block @click="generateAndFetchReferenceNumber"
                        :disabled="isGeneratingReferenceNumber">Generate</v-btn>
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
            <v-data-table-server v-model:items-per-page="itemsPerPage" :page="page" :headers="headers"
                :items-length="totalItems" :items="products" :loading="loading" :reference_number="reference_number"
                :stock_in_date="stock_in_date" :stock_in_by="stock_in_by" @click="addToCartProduct" item-value="id" class="elevation-1">
                <template v-slot:custom-sort="{ header }">
                    <span v-if="header.key === 'actions'">Actions</span>
                </template>
                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{ item.id }}</td>
                        <td>{{ item.reference_number }}</td>
                        <td>{{ item.product_code }}</td>
                        <td>{{ item.barcode }}</td>
                        <td>{{ item.description }}</td>
                        <td>
                            <span v-if="isStockEntryPage">
                                <!-- <span @click="openEditQuantityDialog(index)">{{ item.quantity_added }}</span> -->
                                <span @click="openEditQuantityDialog(item)">{{ item.quantity_added }}</span>
                            </span>
                            <span v-else>{{ item.quantity_added }}</span>
                        </td>
                        <td>{{ item.stock_in_date }}</td>
                        <td>{{ item.stock_in_by }}</td>
                        <td>
                            <span style="margin-left: 2px;">
                                <v-icon @click="showDeleteConfirmation(item)" color="error">mdi-delete</v-icon>
                            </span>
                        </td>
                    </tr>
                </template>
            </v-data-table-server>
            <!-- <CustomTable :columns="tableColumns" :items="products" :showDeleteIcon="true" :isStockEntryPage="true"
                @delete-data="showDeleteConfirmation" @edit-quantity="openEditQuantityDialog"
                @add-to-cart-product="addToCartProduct" :reference_number="reference_number" :stock_in_date="stock_in_date"
                :stock_in_by="stock_in_by" height="450px" /> -->
            <DeleteConfirmationDialog @confirm-delete="deleteProductRow" ref="deleteConfirmationDialog" />
            <v-row class="mt-5 save-btn">
                <v-col cols="2" offset-md="10">
                    <v-btn color="success" @click="showConfirmation" style="width: 150px;"
                        :disabled="isSaveButtonDisabled">Save</v-btn>
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
                            <v-btn color="success" @click="saveRecord" style="width: 150px;">Save</v-btn>
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
                        <v-btn color="primary" @click="saveEditedQuantity"
                            :disabled="isEditQuantitySaveButtonDisabled">Save</v-btn>
                        <v-btn @click="closeEditQuantityDialog">Cancel</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </v-main>
</template>

<script>
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
import BrowseProduct from '../../commons/BrowseProduct.vue';
import { VIcon } from "vuetify/lib/components";
import { mapState } from 'vuex';
import axios from 'axios';
export default {
    components: {
        BrowseProduct,
        DeleteConfirmationDialog,
        VIcon,
    },
    data() {
        return {
            itemsPerPage: 10,
            page: 1,
            id: 1,
            totalItems: 0,
            showEditQuantityDialog: false,
            showConfirmationDialog: false,
            showBrowseProduct: false,
            isGeneratingReferenceNumber: false,
            products: [],
            loading: false,
            editedQuantity: 0,
            editingIndex: -1,
            reference_number: '',
            stock_in_date: '',
            isStockEntryPage: true,
            headers: [
                { title: '#', key: 'id' },
                { title: 'Reference No.', key: 'reference_number' },
                { title: 'Product Code', key: 'product_code' },
                { title: 'Bar Code', key: 'barcode' },
                { title: 'Description', key: 'description' },
                { title: 'Quantity', key: 'quantity_added' },
                { title: 'Stock In Date', key: 'stock_in_date' },
                { title: 'Stock In By', key: 'stock_in_by' },
                { title: 'Actions', key: 'actions', sortable: false }
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
            } else {
                return '';
            }
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
        } else {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth() + 1;
            const day = today.getDate();
            this.stock_in_date = `${year}-${month}-${day}`;
        }
    },

    methods: {
        generateAndFetchReferenceNumber() {
            this.isGeneratingReferenceNumber = true;
            axios.get('/stock-in/generate-reference-number')
                .then(response => {
                    this.reference_number = response.data.reference_number;
                })
                .catch(error => {
                    console.error('Error fetching reference number', error);
                });
        },

        addToCartProduct(product) {
            const reference_number = this.reference_number;
            const stock_in_date = this.stock_in_date;
            const stock_in_by = this.stock_in_by;

            const existingProduct = this.products.find(p => p.product_code === product.product_code);
            if (existingProduct !== undefined) {
                existingProduct.quantity_added++;
            } else {
                const newProduct = {
                    id: product.id,
                    product_code: product.product_code,
                    barcode: product.barcode,
                    description: product.description,
                    reference_number,
                    quantity_added: 1,
                    stock_in_date,
                    stock_in_by,
                };
                this.products.push(newProduct);
                this.loading = false;
            }
        },

        openEditQuantityDialog(item) {
            // Use item here
            this.editingIndex = this.products.indexOf(item);
            this.editedQuantity = item.quantity_added;
            this.showEditQuantityDialog = true;
        },

        saveEditedQuantity() {
            if (this.editingIndex !== -1) {
                this.products[this.editingIndex].quantity_added = this.editedQuantity;
                this.showEditQuantityDialog = false;
                this.editingIndex = -1;
                this.editedQuantity = 0;
            } else {
                console.error('Editing index is invalid');
            }
        },

        closeEditQuantityDialog() {
            this.showEditQuantityDialog = false;
            this.editingIndex = -1;
            this.editedQuantity = 0;
        },

        deleteProductRow(product_code) {
            const index = this.products.find(p => p.product_code === product_code);
            if (index !== -1) {
                this.products.splice(index, 1);
            } else {
                console.error(`Product with product code "${product_code}" not found.`);
            }
        },

        showDeleteConfirmation(item) {
            this.itemToDelete = item;
            this.$refs.deleteConfirmationDialog.showConfirmationDialog();
        },

        saveRecord() {
            this.showConfirmationDialog = false;
            const stockInRequests = this.products.map((product) => {
                return {
                    reference_number: this.reference_number,
                    stock_in_date: this.stock_in_date,
                    stock_in_by: this.stock_in_by,
                    product_id: product.id,
                    quantity_added: product.quantity_added,
                };
            });

            const stockInData = {
                stock_in_requests: stockInRequests,
            };

            axios
                .post("/stock-in", stockInData)
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

        showConfirmation() {
            this.showConfirmationDialog = true;
        },

        cancelSave() {
            this.showConfirmationDialog = false;
        },

        showBrowseProductForm() {
            this.showBrowseProduct = true;
        },

        closeBrowseProductForm() {
            this.showBrowseProduct = false;
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
