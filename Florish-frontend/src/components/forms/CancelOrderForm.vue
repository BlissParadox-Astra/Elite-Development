<template>
    <v-container class="mt-13 showUserForm">
        <v-row justify="center">
            <v-col cols="12">
                <v-form @submit.prevent="submitForm">
                    <v-row>
                        <v-col>
                            <h2 class="text-center mb-4">CANCEL ORDER DETAILS</h2>
                        </v-col>

                        <v-btn icon @click="closeForm" class="close-icon text-right">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-row>

                    <v-col>
                        <h3 class="text-start mb-4">SOLD ITEMS</h3>
                    </v-col>
                    <v-row justify="center">
                        <v-col cols="12" md="6">
                            <v-text-field v-model="id" label="ID" readonly></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="transaction_number" label="Transaction" readonly></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="product_code" label="Product Code" readonly></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="barcode" label="Bar Code" readonly></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-text-field v-model="description" label="Description" readonly></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="price" label="Price" readonly></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="quantity" label="Quantity" readonly></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="total" label="Total" readonly></v-text-field>
                        </v-col>
                    </v-row>
                    <v-col>
                        <h3 class="text-start mb-4">CANCEL ITEM(S)</h3>
                    </v-col>
                    <v-row justify="center">
                        <v-col cols="12" md="6">
                            <v-text-field v-model="cancel_quantity" label="CANCEL QUANTITY"
                                @input="clearFieldErrors('quantity')" :error-messages="cancelQuantityError"
                                :rules="[v => !!v || 'Quantity is required']" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="cancel_by" label="CANCEL BY"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="reasons" label="REASON(S)" @input="clearFieldErrors('reason')"
                                :error-messages="reasonError" :rules="[v => !!v || 'Reason is required']"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-combobox v-model="value" :items="items" label="ADD TO INVENTORY?"></v-combobox>
                        </v-col>
                        <v-col cols="6" class="mt-16">
                            <v-btn type="submit" color="primary" block>
                                CANCEL ORDER
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
export default {
    name: 'CancelOrderForm',
    props: ['initialTransaction'],
    data() {
        return {
            id: this.initialTransaction ? this.initialTransaction.id : '',
            transaction_number: this.initialTransaction ? this.initialTransaction.transaction_number : '',
            product_code: this.initialTransaction ? this.initialTransaction.product_code : '',
            barcode: this.initialTransaction ? this.initialTransaction.barcode : '',
            description: this.initialTransaction ? this.initialTransaction.description : '',
            price: this.initialTransaction ? this.initialTransaction.price : '',
            quantity: this.initialTransaction ? this.initialTransaction.quantity : '',
            total: this.initialTransaction ? parseInt(this.initialTransaction.total) : '',
            cancel_quantity: this.initialTransaction ? this.initialTransaction.cancel_quantity : '',
            cancel_by: this.initialTransaction ? this.initialTransaction.cancel_by : '',
            reasons: this.initialTransaction ? this.initialTransaction.reasons : '',
            // soldTransaction: !!this.initialTransaction,
            items: ['Yes', 'NO'],
            value: 'Select Options',

            cancelQuantityError: "",
            reasonError: "",
        };
    },

    methods: {
        async submitForm() {
            console.log('Submit Form called');
            this.clearErrors();

            if (
                this.cancelQuantityError ||
                this.cancelQuantityError
            ) {
                return;
            }
            const transactionData = {
                id: this.id,
                transaction_number: this.transaction_number,
                product_code: this.product_code,
                barcode: this.barcode,
                description: this.description,
                price: this.price,
                quantity: this.quantity,
                total: this.total,
                cancel_quantity: this.cancel_quantity,
                cancel_by: this.cancel_by,
                reasons: this.reasons,
                items: this.items,
            };
            this.$emit('cancel-order', transactionData);
        },

        resetFormFields() {
            this.id = "";
            this.transaction_number = "";
            this.product_code = "";
            this.barcode = "";
            this.description = "";
            this.price = "";
            this.quantity = "";
            this.total = "";
            this.cancel_quantity = "";
            this.reasons = "";
        },

        clearErrors() {
            this.cancelQuantityError = "";
            this.reasonsError = "";
        },

        clearFieldErrors(fieldName) {
            this[fieldName + 'Error'] = '';
        },

        closeForm() {
            this.showUserForm = false;
            this.$emit("close");
        },
    },
};
</script>
  
<style scoped>
.showUserForm {
    background-color: rgba(114, 165, 104, 0.9);
    z-index: 999;
}

.error-message {
    color: red;
}
</style>
  