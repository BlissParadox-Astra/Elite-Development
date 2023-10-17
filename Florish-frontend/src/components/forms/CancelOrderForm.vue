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
                            <v-text-field label="ID"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Transaction" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Product Code" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Bar Code" required></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-text-field label="Description"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Price" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Quantity"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Total" required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-col>
                        <h3 class="text-start mb-4">CANCEL ITEM(S)</h3>
                    </v-col>
                    <v-row justify="center">
                        <v-col cols="12" md="6">
                            <v-text-field label="VIOD BY" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="CANCEL QUANTITY" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="CANCEL BY"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="REASON(S)" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-combobox v-model="value" :items="items" label="Default"></v-combobox>
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
    props: ['initialUser', 'userTypes'],
    data() {
        return {
            showUserForm: false,
            user_type: this.initialUser ? this.initialUser.user_type : '',
            user_name: this.initialUser ? this.initialUser.user_name : '',
            password: this.initialUser ? this.initialUser.password : '',
            password_confirmation: this.initialUser ? this.initialUser.password_confirmation : '',
            first_name: this.initialUser ? this.initialUser.first_name : '',
            last_name: this.initialUser ? this.initialUser.last_name : '',
            gender: this.initialUser ? this.initialUser.gender : '',
            age: this.initialUser ? parseInt(this.initialUser.age) : '',
            address: this.initialUser ? this.initialUser.address : '',
            contact_number: this.initialUser ? this.initialUser.contact_number : '',
            editingUser: !!this.initialUser,
            items: ['Yes', 'NO'],
            value: 'Select Options',
        };
    },

    methods: {
        async submitForm() {
            console.log('Submit Form called');

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
  