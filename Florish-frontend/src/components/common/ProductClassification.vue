<template>
    <v-container class="mt-14 ShowModule">
        <h2 class="text-center mb-4">{{ title }}</h2>
        <v-row class="d-flex justify-center">
            <v-col cols="12">
                <v-text-field v-model="newItem" :label="inputLabel"></v-text-field>
                <v-text-field v-if="showCategoryCode" v-model="categoryCode" label="Category Code"></v-text-field>
                <div class=" mt-2 button-container">
                    <v-btn color="primary" @click="saveItem">Save</v-btn>
                    <v-btn @click="cancel">Cancel</v-btn>
                </div>
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
    },
    data() {
        return {
            newItem: this.title === "Brand Module" ? (this.product ? this.product.brand : "") :
                this.title === "Category Module" ? (this.product ? this.product.categoryName : "") :
                    "",
            categoryCode: this.title === "Category Module" && this.product ? this.product.categoryCode : "",
        };
    },
    computed: {
        showCategoryCode() {
            return this.title === "Category Module";
        },
    },
    methods: {
        saveItem() {
            if (this.title === "Category Module") {
                if (this.productIndex !== -1) {
                    this.$emit("category-edited", this.newItem, this.categoryCode, this.productIndex);
                } else {
                    this.$emit("category-added", this.newItem, this.categoryCode);
                }
            } else if (this.title === "Brand Module") {
                if (this.productIndex !== -1) {
                    this.$emit("brand-edited", this.newItem, this.productIndex);
                } else {
                    this.$emit("brand-added", this.newItem);
                }
            }
            this.newItem = "";
            this.categoryCode = "";
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
    background-color: rgba(114, 165, 104, 0.9);
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.button-container {
    display: flex;
    justify-content: space-between;
}
</style>
  