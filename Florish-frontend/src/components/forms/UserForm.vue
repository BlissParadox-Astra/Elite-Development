<template>
    <v-container class="mt-1 showUserForm">
        <v-row justify="center">
            <v-col cols="12">
                <div @click="cancelForm" class="close-button">
                    <v-icon color="black">mdi-close</v-icon>
                </div>
                <v-form @submit.prevent="submitForm" class="form">
                    <v-row justify="center" class="bg-teal pa-1">
                        <h2 class="text-center mb-4">{{ editingUser ? 'Edit User' : 'User Module' }}</h2>
                    </v-row>
                    <v-row justify="center" class="bg-teal-darken-2 pa-1">
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="first_name" label="First Name" placeholder="Enter First Name" required
                                :error-messages="firstNameError" @input="clearFieldErrors('firstName')"
                                :rules="[v => !!v || 'First name is required']"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="last_name" label="Last Name" placeholder="Enter Last Name" required
                                :error-messages="lastNameError" @input="clearFieldErrors('lastName')"
                                :rules="[v => !!v || 'Last name is required']"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="username" label="User Name" placeholder="Enter User Name" required
                                :error-messages="userNameError" @input="clearFieldErrors('userName')"
                                :rules="[v => !!v || 'Username is required']"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-select v-model="user_type" :items="userTypes.map(userType => userType.user_type)"
                                label="User Type" :error-messages="userTypeError" @input="clearFieldErrors('userType')">
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="password" :label="passwordLabel" placeholder="Enter Password" required
                                :error-messages="passwordError" @input="clearFieldErrors('password')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="password_confirmation" :label="passwordConfirmationLabel"
                                placeholder="Enter Confirm Password" required :error-messages="confirmPasswordError"
                                @input="clearFieldErrors('confirmPassword')"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-select v-model="gender" :items="['male', 'female', 'other']" label="Gender"
                                placeholder="Select Gender" required :error-messages="genderError"
                                @input="clearFieldErrors('gender')" :rules="[v => !!v || 'Gender is required']"></v-select>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="age" label="Age" placeholder="Enter Age" required
                                :error-messages="ageError" @input="clearFieldErrors('age')"
                                :rules="[v => !!v || 'Age is required']"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="address" label="Address" placeholder="Enter Address" required
                                :error-messages="addressError" @input="clearFieldErrors('address')"
                                :rules="[v => !!v || 'Address is required']"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" lg="6">
                            <v-text-field v-model="contact_number" label="Contact Number" placeholder="Enter Contact Number"
                                required :error-messages="contactNumberError" @input="clearFieldErrors('contactNumber')"
                                :rules="[v => !!v || 'Contact number is required']"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row justify="center" class="bg-teal-darken-1 pa-2">
                        <v-col cols="12" md="6" lg="4">
                            <v-btn type="submit" color="#23b78d" block>
                                {{ editingUser ? 'Save' : 'Submit' }}
                            </v-btn>
                        </v-col>
                        <v-col cols="12" md="6" lg="4">
                            <v-btn type="button" color="#068863" block @click="cancelForm">Cancel</v-btn>
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
            this.clearErrors();
            const userTypeId = this.findUserTypeIdByName(this.user_type);
            if (!userTypeId) {
                this.userTypeError = 'User Type is required.';
            } else if (
                this.userTypeError ||
                this.userNameError ||
                this.firstNameError ||
                this.lastNameError ||
                this.genderError ||
                this.ageError ||
                this.addressError
            ) {
                return;
            }

            const userData = {
                id: this.initialUser ? this.initialUser.id : null,
                user_type_id: userTypeId,
                first_name: this.first_name,
                last_name: this.last_name,
                gender: this.gender,
                age: this.age,
                address: this.address,
                contact_number: this.contact_number,
                username: this.username,
                password: this.password || '',
                password_confirmation: this.password_confirmation || '',
            };

            if (this.password) {
                userData.password = this.password;
            }
            if (this.password_confirmation) {
                userData.password_confirmation = this.password_confirmation;
            }

            if (this.editingUser) {
                this.$emit('update-user', userData);
            } else {
                this.$emit('add-user', userData);
            }
            //     axios
            //         .put(`/user/${this.initialUser.id}`, userData)
            //         .then((response) => {
            //             if (response.status === 200) {
            //                 this.$emit('update', response.data);
            //                 alert(response.data.message);
            //                 this.resetFormFields();
            //                 this.clearErrors();
            //             } else {
            //                 alert(response.data.message);
            //             }
            //         })
            //         .catch((error) => {
            //             if (error.response && error.response.status === 422) {
            //                 const validationErrors = error.response.data.errors;
            //                 this.userTypeError = validationErrors.user_type_id ? validationErrors.user_type_id[0] : '';
            //                 this.userNameError = validationErrors.username ? validationErrors.username[0] : '';
            //                 this.firstNameError = validationErrors.first_name ? validationErrors.first_name[0] : '';
            //                 this.lastNameError = validationErrors.last_name ? validationErrors.last_name[0] : '';
            //                 this.genderError = validationErrors.gender ? validationErrors.gender[0] : '';
            //                 this.ageError = validationErrors.age ? validationErrors.age[0] : '';
            //                 this.addressError = validationErrors.address ? validationErrors.address[0] : '';
            //                 this.contactNumberError = validationErrors.contact_number ? validationErrors.contact_number[0] : '';
            //                 this.passwordError = validationErrors.password ? validationErrors.password[0] : '';
            //                 this.confirmPasswordError = validationErrors.password_confirmation ? validationErrors.password_confirmation[0] : '';
            //             } else {
            //                 console.error(error);
            //             }
            //         });
            // } else {
            //     axios
            //         .post('/user', userData)
            //         .then((response) => {
            //             if (response.status === 200) {
            //                 this.$emit('add', response.data);
            //                 alert(response.data.message);
            //                 this.resetFormFields();
            //                 this.clearErrors();
            //             } else {
            //                 alert(response.data.message);
            //             }
            //         })
            //         .catch((error) => {
            //             if (error.response && error.response.status === 422) {
            //                 const validationErrors = error.response.data.errors;
            //                 this.userTypeError = validationErrors.user_type_id ? validationErrors.user_type_id[0] : '';
            //                 this.userNameError = validationErrors.username ? validationErrors.username[0] : '';
            //                 this.firstNameError = validationErrors.first_name ? validationErrors.first_name[0] : '';
            //                 this.lastNameError = validationErrors.last_name ? validationErrors.last_name[0] : '';
            //                 this.genderError = validationErrors.gender ? validationErrors.gender[0] : '';
            //                 this.ageError = validationErrors.age ? validationErrors.age[0] : '';
            //                 this.addressError = validationErrors.address ? validationErrors.address[0] : '';
            //                 this.contactNumberError = validationErrors.contact_number ? validationErrors.contact_number[0] : '';
            //                 this.passwordError = validationErrors.password ? validationErrors.password[0] : '';
            //                 this.confirmPasswordError = validationErrors.password_confirmation ? validationErrors.password_confirmation[0] : '';
            //             } else {
            //                 console.error(error);
            //             }
            //         });
            // }
        },

        findUserTypeIdByName(userTypeName) {
            const userType = this.userTypes.find(userType => userType.user_type === userTypeName);
            return userType ? userType.id : null;
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

        cancelForm() {
            this.resetFormFields();
            this.editingUser = false;
            this.$emit('cancel');
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
    },
};
</script>
 
<style scoped>
.showUserForm {
    /* background-image: url("../../assets/assets/vuejs.jpg"); */
    /* background-color: #23b78d; */
    z-index: 999;
}

.close-button {
    position: absolute;
    top: 35px;
    right: 30px;
    z-index: 999;
    font-size: larger;
}

.error-messages {
    color: red;
}
</style>
