<template>
    <v-container class="mt-2 showUserForm">
        <v-row justify="center">
            <v-col cols="12">
                <v-form @submit.prevent="submitForm">
                    <h2 class="text-center mb-4">{{ editingUser ? 'Edit User' : 'User Module' }}</h2>
                    <v-row justify="center">
                        <v-col cols="12" md="6">
                            <v-text-field v-model="first_name" label="First Name" placeholder="Enter First Name" required
                                :error-messages="firstNameError" @input="clearFieldErrors('firstName')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="last_name" label="Last Name" placeholder="Enter Last Name" required
                                :error-messages="lastNameError" @input="clearFieldErrors('lastName')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="user_name" label="User Name" placeholder="Enter User Name" required
                                :error-messages="userNameError" @input="clearFieldErrors('userName')"></v-text-field>
                        </v-col>
                        <v-select v-model="user_type" :items="userTypes" label="user_type" item-text="user_type"
                            item-value="id" required></v-select>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="password" label="Password" placeholder="Enter Password" required
                                :error-messages="passwordError" @input="clearFieldErrors('password')"
                                :rules="passwordRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="password_confirmation" label="Confirm Password"
                                placeholder="Enter Confirm Password" required :error-messages="confirmPasswordError"
                                @input="clearFieldErrors('confirmPassword')" :rules="confirmPasswordRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="gender" label="Gender" placeholder="Enter Gender" required
                                :error-messages="genderError" @input="clearFieldErrors('gender')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="age" label="Age" placeholder="Enter Age" required
                                :error-messages="ageError" @input="clearFieldErrors('age')"
                                :rules="ageRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="address" label="Address" placeholder="Enter Address" required
                                :error-messages="addressError" @input="clearFieldErrors('address')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="contact_number" label="Contact Number" placeholder="Enter Contact Number"
                                required :error-messages="contactNumberError"
                                @input="clearFieldErrors('contactNumber')"></v-text-field>
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
import axios from 'axios';
export default {
    name: 'userForm',
    props: ['initialUser', 'userType', 'userName'],
    data() {
        return {
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

            userTypeError: '',
            userNameError: '',
            passwordError: '',
            confirmPasswordError: '',
            firstNameError: '',
            lastNameError: '',
            genderError: '',
            ageError: '',
            addressError: '',
            contactNumberError: '',

            userTypes: [],
        };
    },

    mounted() {
        this.fetchUserTypes(); // Fetch user types when the component is mounted
    },

    methods: {
        async submitForm() {
            console.log('Submit Form called');
            this.clearErrors();
            if (
                this.userTypeError ||
                this.userNameError ||
                this.passwordError ||
                this.firstNameError ||
                this.lastNameError ||
                this.genderError ||
                this.ageError ||
                this.addressError ||
                this.confirmPasswordError
            ) {
                return;
            }
            const userData = {
                user_type_id: this.user_type,
                first_name: this.first_name,
                last_name: this.last_name,
                gender: this.gender,
                age: this.age,
                address: this.address,
                contact_number: this.contact_number,
                username: this.user_name,
                password: this.password,
                password_confirmation: this.password_confirmation,
            };

            if (this.editingUser) {
                // If editing an existing user, make a PUT request
                axios
                    .put(`/user/${this.initialUser.id}`, userData)
                    .then((response) => {
                        if (response.status === 200) {
                            this.$emit('update', response.data);
                            this.resetFormFields();
                            alert(response.data.message);
                        } else {
                            alert(response.data.message);
                        }
                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const validationErrors = error.response.data.errors;
                            this.userTypeError = validationErrors.user_type_id ? validationErrors.user_type_id[0] : '';
                            this.userNameError = validationErrors.username ? validationErrors.username[0] : '';
                            this.firstNameError = validationErrors.first_name ? validationErrors.first_name[0] : '';
                            this.lastNameError = validationErrors.last_name ? validationErrors.last_name[0] : '';
                            this.genderError = validationErrors.gender ? validationErrors.gender[0] : '';
                            this.ageError = validationErrors.age ? validationErrors.age[0] : '';
                            this.addressError = validationErrors.address ? validationErrors.address[0] : '';
                            this.contactNumberError = validationErrors.contact_number ? validationErrors.contact_number[0] : '';
                            this.passwordError = validationErrors.password ? validationErrors.password[0] : '';
                            this.confirmPasswordError = validationErrors.password_confirmation ? validationErrors.password_confirmation[0] : '';
                        } else {
                            console.error(error);
                        }
                    });
            } else {
                // If creating a new user, make a POST request
                axios
                    .post('/user', userData)
                    .then((response) => {
                        if (response.status === 200) {
                            this.$emit('add', response.data);
                            this.resetFormFields();
                            alert(response.data.message);
                        } else {
                            alert(response.data.message);
                        }
                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const validationErrors = error.response.data.errors;
                            this.userTypeError = validationErrors.user_type_id ? validationErrors.user_type_id[0] : '';
                            this.userNameError = validationErrors.username ? validationErrors.username[0] : '';
                            this.firstNameError = validationErrors.first_name ? validationErrors.first_name[0] : '';
                            this.lastNameError = validationErrors.last_name ? validationErrors.last_name[0] : '';
                            this.genderError = validationErrors.gender ? validationErrors.gender[0] : '';
                            this.ageError = validationErrors.age ? validationErrors.age[0] : '';
                            this.addressError = validationErrors.address ? validationErrors.address[0] : '';
                            this.contactNumberError = validationErrors.contact_number ? validationErrors.contact_number[0] : '';
                            this.passwordError = validationErrors.password ? validationErrors.password[0] : '';
                            this.confirmPasswordError = validationErrors.password_confirmation ? validationErrors.password_confirmation[0] : '';
                        } else {
                            console.error(error);
                        }
                    });
            }
        },

        fetchUserTypes() {
            axios.get('/user-types') // Replace with the correct API endpoint
                .then(response => {
                    this.userTypes = response.data;
                })
                .catch(error => {
                    console.error('Error fetching user types:', error);
                });
        },

        cancelForm() {
            this.resetFormFields();
            this.editingUser = false;
            this.$emit('cancel');
        },

        resetFormFields() {
            this.user_type = '';
            this.user_name = '';
            this.password = '';
            this.password_confirmation = '';
            this.first_name = '';
            this.last_name = '';
            this.gender = '';
            this.age = '';
            this.address = '';
            this.contact_number = '';
        },

        clearErrors() {
            this.userTypeError = '';
            this.userNameError = '';
            this.passwordError = '';
            this.confirmPasswordError = '';
            this.firstNameError = '';
            this.lastNameError = '';
            this.genderError = '';
            this.ageError = '';
            this.addressError = '';
            this.contactNumberError = '';
        },
        clearFieldErrors(fieldName) {
            this[fieldName + 'Error'] = '';
        },
    },
    computed: {
        ageRules() {
            return [
                v => !!v || 'Age is required.',
                v => /^\d+$/.test(v) || 'Age must be a valid number.',
            ];
        },
        passwordRules() {
            return [v => !!v || 'Password is required.',
            v => (v && v.length >= 8) || 'Password must be at least 8 characters long.',
            v => (v && /[A-Z]/.test(v)) || 'Password must contain at least one uppercase letter.',
            v => (v && /[a-z]/.test(v)) || 'Password must contain at least one lowercase letter.',
            v => (v && /\d/.test(v)) || 'Password must contain at least one number.',
            v => (v && /[!@#$%^&*]/.test(v)) || 'Password must contain at least one special character (!@#$%^&*).'];
        },

        confirmPasswordRules() {
            return [v => !!v || 'Confirm password is required'];
        },

        contactNumberRules() {
            return [v => !!v || 'Contact Number is required.',];
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
  