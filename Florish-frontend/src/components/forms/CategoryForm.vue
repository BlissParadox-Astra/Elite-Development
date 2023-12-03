<template>
    <v-container class="mt-16 showCategoryForm">
        <v-row class="justify-center">
            <v-col cols="12">
                <div @click="cancelForm" class="close-button">
                    <v-icon color="white">mdi-close</v-icon>
                </div>
                <v-form @submit.prevent="submitForm">
                    <v-row justify="center" class="bg-teal pa-3">
                        <h2 class="text-center mb-4">{{ editingCategory ? 'Edit Category' : 'Category Module' }}</h2>
                    </v-row>
                    <v-row justify="center" class="bg-dirty-white pa-2">
                        <v-col cols="12" lg="12">
                            <v-text-field v-model="category_name" label="Category Name" placeholder="Enter Category Name"
                                required :error-messages="categoryError" @input="clearFieldErrors('categoryName')"
                                :rules="[v => !!v || 'Category Name is required']"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row justify="center" class="bg-dirty-white pa-2">
                        <v-col cols="12" md="6" lg="5">
                            <v-btn type="submit" class="bg-teal" block>
                                {{ editingCategory ? 'Save' : 'Submit' }}
                            </v-btn>
                        </v-col>
                        <v-col cols="12" md="6" lg="5">
                            <v-btn type="button" color="#23b78d" block @click="cancelForm">Cancel</v-btn>
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
    z-index: 999;
    top: 120px;
}

.bg-dirty-white {
    background-color: rgba(233, 224, 224, 0.949);
}

.close-button {
    position: absolute;
    top: 90px;
    right: 35px;
    z-index: 999;
    font-size: larger;
}

.error-messages {
    color: red;
}
</style>