<template>
    <v-container class="mt-2 showProductForm">
        <v-row justify="center">
            <v-col cols="12">
                <v-form @submit.prevent="submitForm">
                    <h2 class="text-center mb-4">Product Module</h2>
                    <v-row justify="center">
                        <v-col cols="12" md="6">
                            <v-text-field v-model="productCode" label="Product Code" placeholder="Enter Product Code"
                                required :rules="productCodeErrorRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="barCode" label="Bar Code" placeholder="Enter Bar Code" required
                                :rules="barCodeErrorRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="description" label="Description" placeholder="Enter Description" required
                                :rules="descriptionErrorRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="brand" label="Brand" placeholder="Enter Brand Name" required
                                :rules="brandErrorRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="category" label="Category" placeholder="Enter Category Name" required
                                :rules="categoryErrorRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="price" label="Price" placeholder="Enter Price" required
                                :rules="priceErrorRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" class="text-center">
                            <v-text-field v-model="reorderLevel" label="Reorder Level" placeholder="Enter Reorder Level"
                                required :rules="reorderLevelErrorRules"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row class="mt-4">
                        <v-col cols="6">
                            <v-btn type="submit" color="primary" block>
                                {{ editingProduct ? 'Save' : 'Submit' }}
                            </v-btn>
                        </v-col>
                        <v-col cols="6">
                            <v-btn type="button" color="secondary" block @click="cancelForm">Cancel</v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
export default {
    name: 'ProductForm',
    props: ['initialProduct'],
    data() {
        return {
            productCode: this.initialProduct ? this.initialProduct.productCode : '',
            barCode: this.initialProduct ? this.initialProduct.barCode : '',
            description: this.initialProduct ? this.initialProduct.description : '',
            brand: this.initialProduct ? this.initialProduct.brand : '',
            category: this.initialProduct ? this.initialProduct.category : '',
            price: this.initialProduct ? this.initialProduct.price : '',
            reorderLevel: this.initialProduct ? this.initialProduct.reorderLevel : '',
            stockOnHand: this.initialProduct ? this.initialProduct.stockOnHand : 0,
            editingProduct: !!this.initialProduct,

            productCodeError: '', // Initialize this property
            barCodeError: '', // Initialize this property
            descriptionError: '', // Initialize this property
            brandError: '', // Initialize this property
            categoryError: '', // Initialize this property
            priceError: '', // Initialize this property
            reorderLevelError: '',
        };
    },
    methods: {
        submitForm() {
            this.clearErrors();
            // Check if there are any errors
            if (!this.productCode) this.productCodeError = 'Product Code is required.';
            if (!this.barCode) this.barCodeError = 'Barcode is required.';
            if (!this.description) this.descriptionError = 'Description is required.';
            if (!this.brand) this.brandError = 'Brand is required.';
            if (!this.category) this.categoryError = 'Category is required.';
            if (!this.price) this.priceError = 'Price is required.';
            if (!this.reorderLevel) this.reorderLevelError = 'Reorder Level is required.';

            // Check if there are any errors
            // if (
            //     this.productCodeError ||
            //     this.barCodeError ||
            //     this.descriptionError ||
            //     this.brandError ||
            //     this.categoryError ||
            //     this.priceError ||
            //     this.reorderLevelError
            // ) {
            //     return; // Prevent form submission
            // }
            const productData = {
                productCode: this.productCode,
                barCode: this.barCode,
                description: this.description,
                brand: this.brand,
                category: this.category,
                price: this.price,
                reorderLevel: this.reorderLevel,
                stockOnHand: this.stockOnHand,
            };
            if (this.editingProduct) {
                this.$emit('update', productData);
            } else {
                this.$emit('add', productData);
            }
            this.resetForm();
        },
        cancelForm() {
            this.resetForm();
            this.editingProduct = false;
            this.$emit('cancel');
        },
        resetForm() {
            this.productCode = '';
            this.barCode = '';
            this.description = '';
            this.brand = '';
            this.category = '';
            this.price = '';
            this.reorderLevel = '';
        },

        clearErrors() {
            this.productCodeError = '';
            this.barCodeError = '';
            this.descriptionError = '';
            this.brandError = '';
            this.categoryError = '';
            this.priceError = '';
            this.reorderLevelError = '';
        }
    },
    computed: {
        productCodeErrorRules() {
            return [v => !!v || 'Product code is required.',];
        },
        barCodeErrorRules() {
            return [v => !!v || 'Bar code is required.',];
        },
        descriptionErrorRules() {
            return [v => !!v || 'Description is required.',];
        },
        brandErrorRules() {
            return [v => !!v || 'Brand is required.',];
        },
        categoryErrorRules() {
            return [v => !!v || 'Category is required.',];
        },
        priceErrorRules() {
            return [v => !!v || 'Price is required.',
            v => /^\d+$/.test(v) || 'Price be a valid number.',
            ];
        },
        reorderLevelErrorRules() {
            return [v => !!v || 'Reorder level is required.',
            v => /^\d+$/.test(v) || 'Reorder level be a valid number.',
            ];
        },
    },
};
</script>
  
<style>
.showProductForm {
    position: fixed;
    top: 50%;
    left: 60%;
    transform: translate(-50%, -50%);
    background-color: rgba(114, 165, 104, 0.9);
    z-index: 999;
    padding: 30px;
    width: 850px;
}
</style>
  