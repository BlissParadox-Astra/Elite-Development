<template>
    <v-container class="mt-2 showBrandForm">
        <v-row class="justify-center">
            <v-col cols="12">
                <v-btn icon @click="cancelForm" class="close-button" color="transparent">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-form @submit.prevent="submitForm">
                    <h2 class="text-center mb-4">{{ editingBrand ? 'Edit Brand' : 'Brand Module' }}</h2>
                    <v-col cols="12" lg="12">
                        <v-text-field v-model="brand_name" label="Brand Name" placeholder="Enter Brand Name" required
                            :error-messages="brandError" @input="clearFieldErrors('brandName')"
                            :rules="[v => !!v || 'Brand Name is required']"></v-text-field>
                        <v-select v-model="category_name"
                            :items="existingCategories.map(category => category.category_name)" label="Categories"
                            placeholder="Choose Category" :error-messages="selectedCategoryError"
                            @input="clearFieldErrors"></v-select>
                    </v-col>
                    <v-row justify="center">
                        <v-col cols="12" md="6" lg="5">
                            <v-btn type="submit" color="primary" block>
                                {{ editingBrand ? 'Save' : 'Submit' }}
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
    name: "BrandForm",
    props: ['initialBrand', 'existingCategories'],
    data() {
        return {
            brand_name: this.initialBrand ? this.initialBrand.brand_name : '',
            category_name: this.initialBrand ? this.initialBrand.category_name : '',
            editingBrand: !!this.initialBrand,
            brandError: '',
            selectedCategoryError: '',
        }
    },
    methods: {
        async submitForm() {
            this.clearErrors();
            const categoryId = this.findCategoryIdByName(this.category_name);
            if (!categoryId) {
                this.selectedCategoryError = 'Category is required'
            } else {
                this.selectedCategoryError = '';
            }
            const brandData = {
                id: this.initialBrand ? this.initialBrand.id : null,
                brand_name: this.brand_name,
                category_id: categoryId,
            };
            if (this.editingBrand) {
                this.$emit('update-brand', brandData);
            } else {
                this.$emit('add-brand', brandData);
            }
        },
        findCategoryIdByName(categoryName) {
            const category = this.existingCategories.find(category => category.category_name === categoryName);
            return category ? category.id : null;
        },
        resetFormFields() {
            this.brand_name = '';
            this.category_name = '';
        },
        clearErrors() {
            this.brandError = '';
            this.selectedCategoryError = '';
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
}
</script>

<style scoped>
.showBrandForm {
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