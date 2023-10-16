<template>
  <v-main>
    <v-container>
      <v-row>
        <v-col cols="12" sm="9">
          <SearchField />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block @click="showCategoryForm">Add Category</v-btn>
        </v-col>
      </v-row>
      <v-row justify="center">
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="categories" :showEditIcon="true" :showDeleteIcon="true"
            @edit-data="editCategoryRow" @delete-data="showDeleteConfirmation" class="custom-table" />
        </v-col>
      </v-row>
      <DeleteConfirmationDialog @confirm-delete="deleteCategory" ref="deleteConfirmationDialog" />
      <v-row>
        <v-col cols="12">
          <v-row class="d-flex justify-center">
            <v-col cols="12" sm="5" xl="5" lg="5" md="5 " class="form-container">
              <CategoryForm v-if="showForm" @add="addCategory" @update="updateCategory(editingCategoryIndex, $event)"
                @cancel="hideCategoryForm" :initialCategory="editingCategory" />
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
 
<script>
import CustomTable from "../../commons/CustomTable.vue";
import SearchField from "../../commons/SearchField.vue";
import CategoryForm from "../../forms/CategoryForm.vue";
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
import axios from 'axios';


export default {
  name: "ProductCategorySection",
  components: {
    CustomTable,
    SearchField,
    CategoryForm,
    DeleteConfirmationDialog,
  },
  data() {
    return {
      showForm: false,
      categories: [],
      editingCategory: null,
      editingCategoryIndex: -1,
      tableColumns: [
        { key: 'category_name', label: 'Category Name' },
      ],
    };
  },


  mounted() {
    this.getCategories();
  },

  methods: {
    getCategories() {
      axios.get('/categories').then(res => {
        this.categories = res.data.categories
      });
    },

    addCategory(categoryData) {
      if (!categoryData.category_name) {
        return;
      }
      this.categories.push(categoryData);
      this.hideCategoryForm();
    },

    editCategoryRow(category) {
      this.editingCategory = { ...category };
      const index = this.categories.findIndex(
        p => p.categoryCode === category.category
      );
      this.editingProductIndex = index;
      this.showForm = true;
    },

    updateCategory(index, updatedCategory) {
      this.categories[index] = updatedCategory;
      this.editingCategory = null;
      this.hideCategoryForm();
    },

    deleteCategory() {
      if (this.itemToDelete) {
        axios.delete(`/category/${this.itemToDelete.id}`)
          .then(() => {
            const index = this.categories.findIndex(category => category.id === this.itemToDelete.id);
            if (index !== -1) {
              this.categories.splice(index, 1);
            }
            this.$refs.deleteConfirmationDialog.closeDialog();
          })
          .catch(error => {
            console.error('Error deleting item:', error);
          });
      }
    },

    showDeleteConfirmation(item) {
      this.itemToDelete = item;
      this.$refs.deleteConfirmationDialog.showConfirmationDialog();
    },

    showCategoryForm() {
      this.showForm = true;
    },

    hideCategoryForm() {
      this.showForm = false;
      this.editingCategory = null;
      this.editingCategoryIndex = -1;
    },


    cancelCategoryAdd() {
      this.showForm = false;
      this.editingProduct = null;
      this.editingProductIndex = -1;
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
  max-height: 100%;
  overflow-y: auto;
}
.custom-table {
  height: 500px;
}
</style>
