<template>
    <v-container class="mt-2 ShowModule">
        <v-row class="justify-center">
            <v-col cols="12">
                <v-form @submit.prevent="saveItem">
                    <h2 class="text-center mb-4">{{ title }}</h2>
                    <v-text-field v-model="newItem" :label="inputLabel"></v-text-field>
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
    },
    data() {
        return {
            newItem: this.title === "Brand Module" ? (this.product ? this.product.brand : "") :
                this.title === "Category Module" ? (this.product ? this.product.categoryName : "") :
                    "",
        };
    },
    methods: {
        saveItem() {
            if (this.title === "Category Module") {
                this.$emit("add-category", this.newItem);
            } else if (this.title === "Brand Module") {
                this.$emit("add-brand", this.newItem);
            }
            // this.newItem = "";
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
  