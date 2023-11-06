<template>
    <v-container class="mt-2 showCategoryForm">
        <v-row class="justify-center">
            <v-col cols="12">
                <v-btn icon @click="cancelForm" class="close-button" color="transparent">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-form @submit.prevent="submitForm">
                    <h2 class="text-center mb-4">{{ editingCategory ? 'Edit Category' : 'Category Module' }}</h2>
                    <v-col cols="12" lg="12">
                        <v-text-field v-model="category_name" label="Category Name" placeholder="Enter Category Name"
                            required :error-messages="categoryError" @input="clearFieldErrors('categoryName')"
                            :rules="[v => !!v || 'Category Name is required']"></v-text-field>
                    </v-col>
                    <v-row justify="center">
                        <v-col cols="12" md="6" lg="5">
                            <v-btn type="submit" color="primary" block>
                                {{ editingCategory ? 'Save' : 'Submit' }}
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
export default {
    name: "CategoryForm",
    props: ['initialCategory'],
    data() {
        return {
            category_name: this.initialCategory ? this.initialCategory.category_name : '',
            editingCategory: !!this.initialCategory,
            categoryError: '',
        }
    },

    methods: {
        async submitForm() {
            this.clearErrors();
            if (this.categoryError) {
                return;
            }
            const categoryData = {
                id: this.initialCategory ? this.initialCategory.id : null,
                category_name: this.category_name,
            };
            if (this.editingCategory) {
                this.$emit('update-category', categoryData);
            } else {
                this.$emit('add-category', categoryData);
            }
        },
        resetFormFields() {
            this.category_name = '';
        },
        clearErrors() {
            this.categoryError = '';
        },
        cancelForm() {
            this.resetFormFields();
            this.editingCategory = false;
            this.$emit('cancel');
        },
        clearFieldErrors(fieldName) {
            this[fieldName + 'Error'] = '';
        },
    }
};
</script>

<style scoped>
.showCategoryForm {
    background-image: url("../../assets/assets/vuejs.jpg");
    z-index: 999;
}

.close-button {
    position: absolute;
    top: 25px;
    right: 20px;
    z-index: 999;
}

.error-messages {
    color: red;
}
</style>