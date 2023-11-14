<template>
    <v-container class="mt-2 showCategoryForm">
        <v-row class="justify-center">
            <v-col cols="12">
                <div @click="closeForm" class="close-button">
                    <v-icon color="white">mdi-close</v-icon>
                </div>
                <v-form @submit.prevent="submitForm">
                    <v-row  justify="center" class="bg-teal pa-3">
                    <h2 class="text-center mb-4">{{ editingCategory ? 'Edit Category' : 'Category Module' }}</h2>
                    </v-row>
                    <v-row justify="center" class="bg-teal-darken-2 pa-2">
                    <v-col cols="12" lg="12">
                        <v-text-field v-model="category_name" label="Category Name" placeholder="Enter Category Name"
                            required :error-messages="categoryError" @input="clearFieldErrors('categoryName')"
                            :rules="[v => !!v || 'Category Name is required']"></v-text-field>
                    </v-col>
                    </v-row>
                    <v-row  justify="center" class="bg-teal-darken-1 pa-2">
                        <v-col cols="12" md="6" lg="5">
                            <v-btn type="submit" color="#4caf50" block>
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
    /* background-image: url("../../assets/assets/vuejs.jpg"); */
    z-index: 999;
}

.close-button {
  position: absolute;
  top: 40px;
  right: 30px;
  z-index: 999;
  font-size: larger;
}

.error-messages {
    color: red;
}
</style>