<template>
    <v-container class="mt-2 ShowModule">
        <v-row class="justify-center">
            <v-col cols="12">
                <v-form @submit.prevent="saveItem">
                    <h2 class="text-center mb-4">{{ title }}</h2>
                    <v-text-field v-model="newItem" :label="inputLabel"></v-text-field>
                    <v-select v-if="showCategories" :items="existingCategories.map(category => category.category_name)"
                        v-model="selectedCategory" label="Categories"></v-select>
                    <div class=" mt-2 button-container">
                        <v-btn color="primary" type="submit">{{ editMode ? 'Save' : 'Add' }}</v-btn>
                        <v-btn @click="cancel">Cancel</v-btn>
                    </div>
                </v-form>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
export default {
    name: "ProductClassification",
    props: {
        title: String,
        inputLabel: {
            type: String,
            default: "New Brand",
        },
        product: Object, // Add this prop
        productIndex: Number, // Add this prop
        editMode: Boolean,
        existingCategories: Array
    },
    data() {
        return {
            newItem: this.title === "Brand Module" ? (this.product ? this.product.brandName : "") :
                this.title === "Category Module" ? (this.product ? this.product.categoryName : "") :
                    "",
            selectedCategory: null
        };
    },
    computed: {
        showCategories() {
            return this.title === "Brand Module";
        },
    },
    methods: {
        saveItem() {
            if (this.title === "Category Module") {
                this.$emit("add-category", this.newItem);
            } else if (this.title === "Brand Module") {
                if (this.selectedCategory) {
                    const categoryId = this.findCategoryIdByName(this.selectedCategory);
                    if (categoryId) {
                        this.$emit("add-brand", this.newItem, categoryId);
                    } else {
                        alert("Please select a category");
                    }
                } else {
                    alert("Please select a category");
                } 
            }
        },
        findCategoryIdByName(categoryName) {
            const category = this.existingCategories.find(category => category.category_name === categoryName);
            return category ? category.id : null;
        },
        cancel() {
            this.$emit("cancel");
            this.newItem = "";
        },
    },
};
</script>
  
<style>
.ShowModule {
    background-image: url("../../assets/assets/vuejs.jpg");
    z-index: 999;
} 
.button-container {
    display: flex;
    justify-content: space-between;
}
</style>
  