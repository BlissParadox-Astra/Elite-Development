<template>
    <v-container class="mt-13 showUserForm">
        <v-row justify="center">
            <v-col cols="12">
                <v-form @submit.prevent="submitForm">
                    <v-row class="align-center bg-teal pa-3">
                        <v-col>
                            <h2 class="text-center mb-4">CANCEL ORDER DETAILS</h2>
                        </v-col>
                        <div @click="closeForm" class="close-button">
                            <v-icon color="white">mdi-close</v-icon>
                        </div>
                    </v-row>
                    <v-row class="bg-teal-darken-2 pa-2">
                        <v-col cols="12">
                            <h3 class="mb-4">SOLD ITEMS</h3>
                            <v-row>
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
                        </v-col>
                   
                        <v-col cols="12">
                            <h3 class="mb-4">CANCEL ITEM(S)</h3>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="cancel_quantity" label="CANCEL QUANTITY"
                                    @input="clearFieldErrors('quantity')" :error-messages="cancelQuantityError"
                                    :rules="[v => !!v || 'Quantity is required']" required></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field :model-value="cancel_by" label="CANCEL BY" readonly></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="reasons" label="REASON(S)" @input="clearFieldErrors('reason')"
                                    :error-messages="reasonError" :rules="[v => !!v || 'Reason is required']"
                                    required></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select v-model="options" :error-messages="actionTakenError"
                                    :items="commandOptions"></v-select>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                    <v-row justify="center" class="bg-teal-darken-1 pa-2">
                        <v-col cols="6" class="mt-5">
                            <v-btn type="submit" color="#23b78d" block :disabled="isCancelButtonDisabled">
                                CANCEL ORDER
                            </v-btn>
                        </v-col>
                    </v-row>

                </v-form>
            </v-col>
        </v-row>
        <v-dialog v-model="showConfirmationDialog" max-width="500px">
            <v-card>
                <v-card-title class="headline">
                    Confirm Cancel Order
                </v-card-title>
                <v-card-text>
                    ARE YOU SURE YOU WANT CANCEL THIS ORDER?
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="cancelOrderConfirmed" color="primary">
                        Yes
                    </v-btn>
                    <v-btn @click="cancelOrderCancelled" color="red darken-1">
                        No
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
  
<script>
import { mapState } from 'vuex';
export default {
    name: 'CancelOrderForm',
    props: {
        initialTransaction: {
            type: Object,
            default: null,
        },
        currentUser: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            id: this.initialTransaction ? this.initialTransaction.id : '',
            transaction_number: this.initialTransaction ? this.initialTransaction.transaction_number : '',
            product_code: this.initialTransaction ? this.initialTransaction.transacted_product.product_code : '',
            barcode: this.initialTransaction ? this.initialTransaction.transacted_product.barcode : '',
            description: this.initialTransaction ? this.initialTransaction.transacted_product.description : '',
            price: this.initialTransaction ? this.initialTransaction.price : '',
            quantity: this.initialTransaction ? this.initialTransaction.quantity : '',
            total: this.initialTransaction ? parseInt(this.initialTransaction.total) : '',
            cancel_quantity: this.initialTransaction ? this.initialTransaction.cancel_quantity : '',
            reasons: this.initialTransaction ? this.initialTransaction.reasons : '',

            commandOptions: ["Yes", "No"],

            options: "ADD TO INVENTORY?",

            cancelQuantityError: "",
            reasonError: "",
            actionTakenError: "",
            showConfirmationDialog: false,
            disableCancelButton: false,
        };
    },

    computed: {
        ...mapState({
            user: state => state.user,
        }),

        cancel_by() {
            if (this.user && this.user.first_name && this.user.last_name) {
                return `${this.user.first_name} ${this.user.last_name}`;
            } else {
                return '';
            }
        },

        isCancelButtonDisabled() {
            return (
                !this.cancel_quantity ||
                !this.cancel_by ||
                !this.reasons ||
                !this.options || this.options === "ADD TO INVENTORY?"
            );
        },
    },

    methods: {
        async submitForm() {
            this.clearErrors();
            if (!this.options || this.options === "ADD TO INVENTORY?") {
                this.actionTakenError = "Action taken is required";
            } else if (
                this.cancelQuantityError ||
                this.cancelQuantityError ||
                this.actionTakenError
            ) {
                return;
            }
            this.showConfirmationDialog = true;
            // const transactionData = {
            //     transaction_id: this.id,
            //     total: this.total,
            //     quantity: this.cancel_quantity,
            //     cancel_by: this.cancel_by,
            //     reason: this.reasons,
            //     action_taken: this.options,
            // };
            // this.$emit('cancel-order', transactionData);
        },

        cancelOrderConfirmed() {
            this.showConfirmationDialog = false;

            const transactionData = {
                transaction_id: this.id,
                total: this.total,
                quantity: this.cancel_quantity,
                cancel_by: this.cancel_by,
                reason: this.reasons,
                action_taken: this.options,
            };

            this.$emit('cancel-order', transactionData);
        },

        cancelOrderCancelled() {
            this.showConfirmationDialog = false;
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
            this.actionTakenError = "";
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
    /* background-color: rgba(114, 165, 104, 0.9); */
    z-index: 999;
}

.close-button {
    position: absolute;
    top: 90px;
    right: 40px;
    z-index: 999;
    font-size: x-large;
}

.error-message {
    color: red;
}
</style>
  