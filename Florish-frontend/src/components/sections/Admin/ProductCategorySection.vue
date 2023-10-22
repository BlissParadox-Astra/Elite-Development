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
          <v-data-table-server v-model:items-per-page="itemsPerPage" :page="page" :headers="headers"
            :items-length="totalItems" :items="categories" :loading="loading" item-value="id" class="elevation-1"
            @update:options="getCategories">
            <template v-slot:custom-sort="{ header }">
              <span v-if="header.key === 'actions'">Actions</span>
            </template>
            <template v-slot:item="{ item }">
              <tr>
                <td>{{ item.id }}</td>
                <td>{{ item.category_name }}</td>
                <td>
                  <span>
                    <v-icon @click="editCategoryRow(item)" color="primary">mdi-pencil</v-icon>
                  </span>
                  <span style="margin-left: 15px;">
                    <v-icon @click="showDeleteConfirmation(item)" color="error">mdi-delete</v-icon>
                  </span>
                </td>
              </tr>
            </template>
          </v-data-table-server>
          <!-- <p>Current Page: {{ page }}</p> -->
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
import SearchField from "../../commons/SearchField.vue";
import CategoryForm from "../../forms/CategoryForm.vue";
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
import axios from 'axios';


export default {
  name: "ProductCategorySection",
  components: {
    SearchField,
    CategoryForm,
    DeleteConfirmationDialog,
  },
  data() {
    return {
      itemsPerPage: 10,
      page: 1,
      showForm: false,
      categories: [],
      totalItems: 0,
      editingCategory: null,
      editingCategoryIndex: -1,
      loading: true,
      id: 1,
      headers: [
        { title: '#', key: 'id' },
        { title: 'Category Name', key: 'category_name' },
        { title: 'Actions', key: 'actions', sortable: false }
      ],
    };
  },

  mounted() {
    this.getCategories();
  },

  methods: {
    getCategories() {
      this.loading = true;
      axios
        .get('/categories', {
          params: {
            page: this.page,
            itemsPerPage: this.itemsPerPage,
          }
        })
        .then((res) => {
          // console.log("API Response - Current Page:", this.page);
          this.categories = res.data.categories;
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching categories:', error);
        });
        // console.log("After API Request - Current Page:", this.page);
    },

    addCategory(categoryData) {
      if (!categoryData.category_name) {
        return;
      }
      categoryData.id = this.id;
      this.id++;
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

/* .custom-table {
  height: 500px;
} */
</style>
