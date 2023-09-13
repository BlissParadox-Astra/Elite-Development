<template>
    <v-container class="mt-2 showUserForm">
        <v-row justify="center">
            <v-col cols="12">
                <v-btn icon @click="cancelForm" class="close-button" color="transparent">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-form @submit.prevent="submitForm">
                    <h2 class="text-center mb-4">{{ editingUser ? 'Edit User' : 'User Module' }}</h2>
                    <v-row justify="center">
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="first_name" label="First Name" placeholder="Enter First Name" required
                                :error-messages="firstNameError" @input="clearFieldErrors('firstName')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="last_name" label="Last Name" placeholder="Enter Last Name" required
                                :error-messages="lastNameError" @input="clearFieldErrors('lastName')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="username" label="User Name" placeholder="Enter User Name" required
                                :error-messages="userNameError" @input="clearFieldErrors('userName')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-select v-model="user_type" :items="userTypes.map(userType => userType.user_type)"
                                label="User Type" :error-messages="userTypeError" @input="clearFieldErrors('userType')">
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="password" :label="passwordLabel" placeholder="Enter Password" required
                                :error-messages="passwordError" @input="clearFieldErrors('password')"
                                :rules="passwordRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="password_confirmation" :label="passwordConfirmationLabel"
                                placeholder="Enter Confirm Password" required :error-messages="confirmPasswordError"
                                @input="clearFieldErrors('confirmPassword')" :rules="confirmPasswordRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-select v-model="gender" :items="['male', 'female', 'other']" label="Gender"
                                placeholder="Select Gender" required :error-messages="genderError"
                                @input="clearFieldErrors('gender')"></v-select>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="age" label="Age" placeholder="Enter Age" required
                                :error-messages="ageError" @input="clearFieldErrors('age')"
                                :rules="ageRules"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="address" label="Address" placeholder="Enter Address" required
                                :error-messages="addressError" @input="clearFieldErrors('address')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-text-field v-model="contact_number" label="Contact Number" placeholder="Enter Contact Number"
                                required :error-messages="contactNumberError"
                                @input="clearFieldErrors('contactNumber')"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row justify="center">
                        <v-col cols="12" md="6" lg="5">
                            <v-btn type="submit" color="primary" block>
                                {{ editingUser ? 'Save' : 'Submit' }}
                            </v-btn>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
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
    props: ['initialUser', 'userTypes'],
    data() {
        return {
            user_type: this.initialUser ? this.initialUser.user_type : '',
            username: this.initialUser ? this.initialUser.username : '',
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
        };
    },


    methods: {
        async submitForm() {
            console.log('Submit Form called');
            this.clearErrors();
            const userTypeId = this.findUserTypeIdByName(this.user_type);
            if (!userTypeId) {
                this.userTypeError = 'User Type is required.';
            }
            else if (
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
                user_type_id: userTypeId,
                first_name: this.first_name,
                last_name: this.last_name,
                gender: this.gender,
                age: this.age,
                address: this.address,
                contact_number: this.contact_number,
                username: this.username,
                password: this.password,
                password_confirmation: this.password_confirmation,
            };

            if (this.editingUser) {
                axios
                    .put(`/user/${this.initialUser.id}`, userData)
                    .then((response) => {
                        if (response.status === 200) {
                            this.$emit('update', response.data);
                            this.resetFormFields();
                            alert(response.data.message);
                            this.clearErrors();
                            this.reloadPage();
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
                axios
                    .post('/user', userData)
                    .then((response) => {
                        if (response.status === 200) {
                            this.$emit('add', response.data);
                            this.resetFormFields();
                            alert(response.data.message);
                            this.clearErrors();
                            this.reloadPage();
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

        findUserTypeIdByName(userTypeName) {
            const userType = this.userTypes.find(userType => userType.user_type === userTypeName);
            return userType ? userType.id : null;
        },

        cancelForm() {
            this.resetFormFields();
            this.editingUser = false;
            this.$emit('cancel');
        },

        resetFormFields() {
            this.user_type = '';
            this.username = '';
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


        reloadPage() {
            window.location.reload();
        },
    },

    computed: {
        passwordLabel() {
            return this.editingUser ? 'New Password' : 'Password';
        },

        passwordConfirmationLabel() {
            return this.editingUser ? 'Confirm New Password' : 'Confirm Password';
        },

        userTypeDisplayName() {
            const userType = this.userTypes.find(type => type.id === this.user_type);
            return userType ? userType.user_type : '';
        },

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
    background-image: url("../../assets/assets/vuejs.jpg");
    z-index: 999;
}

.close-button {
    position: absolute;
    top: 25px;
    right: 20px;
    z-index: 999;
    z-index: 999;
}

.error-messages {
    color: red;
}
</style>
