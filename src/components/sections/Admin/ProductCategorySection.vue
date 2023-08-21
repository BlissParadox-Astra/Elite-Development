<template>
  <v-main>
    <v-container class="mt-5">
      <v-row justify="center">
        <v-col cols="12">
          <h2 class="text-center">CATEGORIES</h2>
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <v-row>
        <v-col cols="12" sm="9">
          <SearchField />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block @click="showForm = true">Add Category</v-btn>
        </v-col>
        <ProductClassification v-if="showForm" title="Category Module" :input-label="categoryInputLabel"
          :product="editingProduct" :product-index="editingProductIndex" @category-edited="handleCategoryEdited"
          @category-added="handleCategoryAdded" @cancel="cancelCategoryAdd" :initialProduct="editingProduct" />
      </v-row>
      <v-row>
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="products" :showEditIcon="true" :showDeleteIcon="true"
            @edit-data="editProductRow" @delete-data="deleteProductRow" height="500px"/>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
  
<script>
import CustomTable from "../../common/CustomTable.vue";
import SearchField from "../../common/SearchField.vue";
import ProductClassification from "../../common/ProductClassification.vue";

export default {
  name: "ProductCategorySection",
  components: {
    CustomTable,
    SearchField,
    ProductClassification,
  },
  data() {
    return {
      editingProduct: null,
      editingProductIndex: -1,
      showForm: false,
      tableColumns: [
        { key: "category", label: "Category" },
      ],
      products: [
        { category: "BREVERAGES" },
        { category: "can goods" },
      ],
      categoryInputLabel: "New Category",
    };
  },
  methods: {
    handleCategoryAdded(newCategory) {
      if (this.editingProductIndex !== -1) {
        this.products[this.editingProductIndex].category = newCategory;
        this.editingProduct = null;
        this.editingProductIndex = -1;
      } else {
        this.products.push({ category: newCategory });
      }
      this.showForm = false;
    },
    handleCategoryEdited(newCategory, index) {
      if (index !== -1) {
        this.products[index].category = newCategory;
      }
      this.showForm = false;
      this.editingProduct = null;
      this.editingProductIndex = -1;
    },
    cancelCategoryAdd() {
      this.showForm = false;
      this.editingProduct = null;  // Reset editingProduct
      this.editingProductIndex = -1; // Reset editingProductIndex
    },
    editProductRow(product) {
      this.editingProduct = { ...product }; // Use spread operator to copy the entire object
      const index = this.products.findIndex(p => p.category === product.category);
      this.editingProductIndex = index;
      this.showForm = true;
    },
    deleteProductRow(product) {
      const index = this.products.findIndex(p => p.category === product.category);
      if (index !== -1) {
        this.products.splice(index, 1);
      }
    },
  },
};
</script>
  