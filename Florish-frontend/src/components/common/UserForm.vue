<template>
    <v-container class="mt-2 showUserForm">
        <v-row justify="center">
            <v-col cols="12">
                <v-form @submit.prevent="submitForm">
                    <h2 class="text-center mb-4">User Module</h2>
                    <v-row justify="center">
                        <v-col cols="12" md="6">
                            <v-text-field v-model="firstName" label="First Name" placeholder="Enter First Name" required
                                :rules="firstNameRules"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="lastName" label="Last Name" placeholder="Enter Last Name" required
                                :rules="lastNameRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="userName" label="User Name" placeholder="Enter User Name" required
                                :rules="userNameRules"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="password" label="Password" placeholder="Enter Password" required
                                :rules="passwordRules"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="userType" label="User Type" placeholder="Enter User Type" required
                                :rules="userTypeRules"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="gender" label="Gender" placeholder="Enter Gender" required
                                :rules="genderRules"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="age" label="Age" placeholder="Enter Age"
                                :rules="ageRules"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="6" class="text-center">
                            <v-text-field v-model="address" label="Address" placeholder="Enter Address" required
                                :rules="addressRules"></v-text-field>

                        </v-col>
                    </v-row>
                    <v-row class="mt-4">
                        <v-col cols="6">
                            <v-btn type="submit" color="primary" block>
                                {{ editingUser ? 'Save' : 'Submit' }}
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
    name: 'userForm',
    props: ['initialUser'],
    data() {
        return {
            userType: this.initialUser ? this.initialUser.userType : '',
            userName: this.initialUser ? this.initialUser.userName : '',
            password: this.initialUser ? this.initialUser.password : '',
            firstName: this.initialUser ? this.initialUser.firstName : '',
            lastName: this.initialUser ? this.initialUser.lastName : '',
            gender: this.initialUser ? this.initialUser.gender : '',
            age: this.initialUser ? parseInt(this.initialUser.age) : '',
            address: this.initialUser ? this.initialUser.address : '',
            editingUser: !!this.initialUser,
            userTypeError: '',
            userNameError: '',
            passwordError: '',
            firstNameError: '',
            lastNameError: '',
            genderError: '',
            ageError: '',
            addressError: '',
        };
    },

    methods: {
        submitForm() {
            console.log('Submitting form...');
            this.clearErrors();

            if (!this.userType) this.userTypeError = 'User Type is required.';
            if (!this.userName) this.userNameError = 'User Name is required.';
            if (!this.firstName) this.firstNameError = 'First Name is required.';
            if (!this.lastName) this.lastNameError = 'Last Name is required.';
            if (!this.gender) this.genderError = 'Gender is required.';
            if (!this.address) this.addressError = 'Address is required.';

            if (
                this.userTypeError ||
                this.userNameError ||
                this.passwordError ||
                this.firstNameError ||
                this.lastNameError ||
                this.genderError ||
                this.ageError ||
                this.addressError
            ) {
                return;
            }

            const userData = {
                userType: this.userType,
                userName: this.userName,
                password: this.password,
                firstName: this.firstName,
                lastName: this.lastName,
                gender: this.gender,
                age: this.age,
                address: this.address,
            };

            if (this.editingUser) {
                this.$emit('update', userData);
            } else {
                this.$emit('add', userData);
            }
            this.resetForm();
        },

        cancelForm() {
            this.resetForm();
            this.editingUser = false;
            this.$emit('cancel');
        },

        resetForm() {
            this.userType = '';
            this.userName = '';
            this.password = '';
            this.firstName = '';
            this.lastName = '';
            this.gender = '';
            this.age = '';
            this.address = '';
        },

        clearErrors() {
            this.userTypeError = '';
            this.userNameError = '';
            this.passwordError = '';
            this.firstNameError = '';
            this.lastNameError = '';
            this.genderError = '';
            this.ageError = '';
            this.addressError = '';
        },
    },

    computed: {
        ageRules() {
            return [
                v => !!v || 'Age is required.',
                v => /^\d+$/.test(v) || 'Age must be a valid number.',
            ];
        },

        userTypeRules() {
            return [
                v => !!v || 'User Type is required.',
            ];
        },

        userNameRules() {
            return [v => !!v || 'User Name is required.',];
        },

        passwordRules() {
            return [v => !!v || 'password is required.',
            v => (v && v.length >= 8) || 'Password must be at least 8 characters long.',
            v => (v && /[A-Z]/.test(v)) || 'Password must contain at least one uppercase letter.',
            v => (v && /[a-z]/.test(v)) || 'Password must contain at least one lowercase letter.',
            v => (v && /\d/.test(v)) || 'Password must contain at least one number.',
            v => (v && /[!@#$%^&*]/.test(v)) || 'Password must contain at least one special character (!@#$%^&*).'];
        },

        firstNameRules() {
            return [v => !!v || 'First Name is required.',];
        },

        lastNameRules() {
            return [v => !!v || 'Last Name is required.',];
        },

        genderRules() {
            return [v => !!v || 'Gender is required.',];
        },

        addressRules() {
            return [v => !!v || 'Address is required.',];
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
  