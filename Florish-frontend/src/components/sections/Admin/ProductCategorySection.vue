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
            @edit-data="editProductRow" @delete-data="deleteProductRow" height="500px" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-row class="d-flex justify-center">
            <v-col cols="12" sm="5" xl="5" lg="5" md="5" class="mt-5 form-container">
              <ProductClassification v-if="showForm" :title="formTitle" :input-label="categoryInputLabel"
                :product="editingProduct" :product-index="editingProductIndex" @category-edited="handleCategoryEdited"
                @add-category="handleCategoryAdded" @cancel="cancelCategoryAdd" :initialProduct="editingProduct" />
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

    handleCategoryAdded(newCategory) {
      // if (this.editingProductIndex !== -1) {
      //   this.categories[this.editingProductIndex].category_name = newCategory;
      //   this.editingProduct = null;
      //   this.editingProductIndex = -1;
      // } else {
      const categoryData = {
        category_name: newCategory,
      };
      axios.post('/category', categoryData)
        .then(response => {
          this.categories.push(response.data);
          alert(response.data.message);
          this.reloadPage();
        })
        .catch(error => {
          console.error('Error adding category:', error);
        });
      this.showForm = false;
    },

    handleCategoryEdited(newCategory, index) {
      if (index !== -1) {
        this.categories[index].category_name = newCategory;
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
        p => p.category_name === product.category_name
      );
      this.editingProductIndex = index;
      this.showForm = true;
    },

    reloadPage() {
      window.location.reload();
    },

    deleteProductRow(product) {
      const index = this.categories.findIndex(
        p => p.category_name === product.category_name
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
