<template>
  <v-main>
    <v-container>
      <v-row>
        <v-col cols="12" sm="9">
          <SearchField />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block @click="showForm = true">Add Category</v-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="categories" :showEditIcon="true" :showDeleteIcon="true"
            @edit-data="editProductRow" @delete-data="deleteProductRow" :itemsPerPage="10" height="500px" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-row class="d-flex justify-center">
            <v-col cols="12" sm="5" xl="5" lg="5" md="5" class="mt-5 form-container">
              <ProductClassification v-if="showForm" :title="formTitle" :input-label="categoryInputLabel"
                :product="editingProduct" :product-index="editingProductIndex" @category-edited="handleCategoryEdited"
                @category-added="handleCategoryAdded" @cancel="cancelCategoryAdd" :initialProduct="editingProduct" />
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
  
<script>
import CustomTable from "../../common/CustomTable.vue";
import SearchField from "../../common/SearchField.vue";
import ProductClassification from "../../common/ProductClassification.vue";
import axios from 'axios';

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
      formTitle: "Category Module",
      categories: [],
      tableColumns: [
        { key: 'category_name', label: 'Category Name' },
        { key: 'category_code', label: 'Category Code' },
      ],
      categoryInputLabel: "Category Name",
    };
  },

  mounted() {
        this.getCategories();
    },

  methods: {
    getCategories() {
            axios.get('/categories').then(res => {
                this.categories = res.data.categories
                console.log(this.categories)
            });
        },

    handleCategoryAdded(newCategory, newCategoryCode) {
      if (this.editingProductIndex !== -1) {
        this.categories[this.editingProductIndex].categoryName = newCategory;
        this.categories[this.editingProductIndex].categoryCode = newCategoryCode;
        this.editingProduct = null;
        this.editingProductIndex = -1;
      } else {
        this.categories.push({ categoryName: newCategory, categoryCode: newCategoryCode });
      }
      this.showForm = false;
    },
    handleCategoryEdited(newCategory, newCategoryCode, index) {
      if (index !== -1) {
        this.categories[index].categoryName = newCategory;
        this.categories[index].categoryCode = newCategoryCode;
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
      const index = this.categories.findIndex(
        p => p.categoryName === product.categoryName && p.categoryCode === product.categoryCode
      );
      this.editingProductIndex = index;
      this.showForm = true;
    },

    deleteProductRow(product) {
      const index = this.categories.findIndex(
        p => p.categoryName === product.categoryName && p.categoryCode === product.categoryCode
      );
      if (index !== -1) {
        this.categories.splice(index, 1);
      }
    },
  },
};
</script>
<style scooped>
.form-container {
  position: absolute;
  top: 0;
  left: 1;
  right: 1;
  z-index: 999;

}
</style>