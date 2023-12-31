<template>
  <v-container class="mt-13">
    <v-row justify="space-between">
      <v-col cols="12" sm="9" md="5" lg="5">
        <SearchField @search="handleSearch" :searchLabel="searchLabel" :searchType="'regular'" />
      </v-col>
      <v-col cols="12" sm="3" class="d-flex justify-center align-center">
        <v-btn color="#23b78d" block @click="showCategoryForm">Add Category</v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12">
        <v-data-table :headers="headers" :items="categories" :loading="loading" :page="currentPage"
          :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
          @update:options="debouncedGetCategories" fixed-header height="400">
          <template v-slot:custom-sort="{ header }">
            <span v-if="header.key === 'actions'">Actions</span>
          </template>
          <template v-slot:item="{ item, index }">
            <tr>
              <td>{{ displayedIndex + index }}</td>
              <td>{{ item.category_name }}</td>
              <td>
                <span>
                  <v-icon @click="editCategoryRow(item)" color="#23b78d">mdi-pencil</v-icon>
                </span>
                <span style="margin-left: 5px;">
                  <v-icon @click="showDeleteConfirmation(item)" color="#23b78d">mdi-delete</v-icon>
                </span>
              </td>
            </tr>
          </template>
          <template v-slot:bottom>
            <v-col cols="12">
              <div v-if="totalPages > 1" class="text-center pt-5 pagination">
                <v-btn class="pagination-button" @click="previousPage" color="#23b78d" :disabled="currentPage === 1">
                  <v-icon>mdi-chevron-left</v-icon> Prev
                </v-btn>

                <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                  :class="{ active: pageNumber === currentPage }" class="pagination-button">
                  {{ pageNumber }}
                </v-btn>

                <v-btn class="pagination-button" @click="nextPage" color="#23b78d" :disabled="currentPage === totalPages">
                  Next <v-icon>mdi-chevron-right</v-icon>
                </v-btn>
              </div>
            </v-col>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <DeleteConfirmationDialog @confirm-delete="deleteCategory" ref="deleteConfirmationDialog" />
    <v-row>
      <v-col cols="12">
        <v-row class="d-flex justify-center">
          <v-col cols="12" sm="5" xl="5" lg="5" md="5 " class="form-container">
            <CategoryForm v-if="showForm" @add-category="addCategory" @update-category="updateCategory"
              @cancel="hideCategoryForm" :initialCategory="editingCategory" />
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-snackbar v-model="snackbar" right top :color="snackbarColor">
      {{ snackbarText }}
      <template v-slot:actions>
        <v-btn color="pink" variant="text" @click="snackbar = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>
 
<script>
import SearchField from "../../commons/SearchField.vue";
import CategoryForm from "../../forms/CategoryForm.vue";
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
import _debounce from 'lodash/debounce';
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
      currentPage: 1,
      showForm: false,
      categories: [],
      totalItems: 0,
      editingCategory: null,
      editingCategoryIndex: -1,
      loading: true,
      id: 1,
      snackbar: false,
      snackbarColor: '',
      searchQuery: '',
      searchLabel: 'Search Category Here',
      headers: [
        { title: '#', value: 'index' },
        { title: 'Category Name', key: 'category_name' },
        { title: 'Actions', key: 'actions', sortable: false }
      ],
    };
  },

  computed: {
    displayedIndex() {
      return (this.currentPage - 1) * this.itemsPerPage + 1;
    },

    totalPages() {
      return Math.ceil(this.totalItems / this.itemsPerPage);
    },

    visiblePageRange() {
      const smallScreenMaxPages = 2;
      const largeScreenMaxPages = 5;

      const maxVisiblePages = this.isSmallScreen ? smallScreenMaxPages : largeScreenMaxPages;
      const halfMaxVisiblePages = Math.floor(maxVisiblePages / 2);
      const firstPage = Math.max(1, this.currentPage - halfMaxVisiblePages);
      const lastPage = Math.min(this.totalPages, firstPage + maxVisiblePages - 1);

      const range = [];
      for (let i = firstPage; i <= lastPage; i++) {
        range.push(i);
      }

      return range;
    },

    isSmallScreen() {
      return window.innerWidth < 768;
    },
  },

  async mounted() {
    await this.debouncedGetCategories();
    // this.$nextTick(() => {
    //   this.$refs.searchField.$refs.searchField.focus();
    // });
  },

  methods: {
    debouncedGetCategories: _debounce(function () {
      this.getCategories();
    }, 1000),

    handleSearch(query) {
      this.searchQuery = query;
      this.currentPage = 1;
      this.debouncedGetCategories();
    },

    getCategories() {
      this.loading = true;
      axios
        .get('/categories', {
          params: {
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
            search: this.searchQuery,
          }
        })
        .then((res) => {
          this.categories = res.data.categories;
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching categories:', error);
        });
    },

    previousPage() {
      this.loading = true;
      if (this.currentPage > 1) {
        this.currentPage--;
        this.debouncedGetCategories();
      }
    },

    nextPage() {
      this.loading = true;
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.debouncedGetCategories();
      }
    },

    gotoPage(pageNumber) {
      this.loading = true;
      this.currentPage = pageNumber;
      this.debouncedGetCategories();
    },

    async addCategory(categoryData) {
      try {
        const response = await axios.post('/category', categoryData);
        if (response.status === 201) {
          this.categories.push(response.data);
          this.hideCategoryForm();
          this.snackbarColor = 'success';
          this.showSnackbar(response.data.message, 'success');
          this.getCategories();
        } else {
          this.snackbarColor = 'error';
          this.showSnackbar(response.data.message, 'error');
        }
      } catch (error) {
        console.error('Error adding category:', error);
        this.snackbarColor = 'error';
        this.showSnackbar(error.response.data.message, 'error');
      }
    },

    editCategoryRow(category) {
      this.editingCategory = {
        ...category,
        id: category.id,
      };
      const index = this.categories.findIndex(
        p => p.categoryCode === category.category
      );
      this.editingProductIndex = index;
      this.showForm = true;
    },

    async updateCategory(categoryData) {
      try {
        const response = await axios.put(`/category/${categoryData.id}`, categoryData);
        if (response.status === 200) {
          this.categories[this.index] = categoryData;
          this.hideCategoryForm();
          this.snackbarColor = 'success';
          this.showSnackbar(response.data.message, 'success');
          this.editingCategory = null;
          this.editingCategoryIndex = -1;
          this.getCategories();
        } else {
          this.snackbarColor = 'error';
          this.showSnackbar(response.data.message, 'error');
        }
      } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 422) {
          const validationErrors = error.response.data.errors;
          this.showSnackbar(
            validationErrors.category_name
              ? validationErrors.category_name[0]
              : 'An error occurred',
            'error'
          );
        } else {
          this.snackbarText = error.response.data.error;
          this.snackbarColor = 'error';
          this.showSnackbar(this.snackbarText, 'error');
        }
      }
    },

    async deleteCategory() {
      if (this.itemToDelete) {
        axios.delete(`/category/${this.itemToDelete.id}`)
          .then((response) => {
            const index = this.categories.findIndex(category => category.id === this.itemToDelete.id);
            if (index !== -1) {
              this.categories.splice(index, 1);
            }
            this.$refs.deleteConfirmationDialog.closeDialog();
            this.showSnackbar(response.data.message, 'success');
            this.getCategories();
          })
          .catch((error) => {
            if (error.response.data.error === 'Cannot delete category. In use by other records.') {
              this.showSnackbar('Cannot delete category. In use by other records.', 'error');
            } else {
              this.showSnackbar(error.response.data.message, 'error');
            }
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
      this.getCategories();
    },

    cancelCategoryAdd() {
      this.showForm = false;
      this.editingProduct = null;
      this.editingProductIndex = -1;
    },

    showSnackbar(text, color, timeout = 3000) {
      this.snackbarText = text;
      this.snackbarColor = color;
      this.snackbar = true;

      setTimeout(() => {
        this.hideSnackbar();
      }, timeout);
    },

    hideSnackbar() {
      this.snackbar = false;
    },
  },
};
</script>

<style scoped>
.form-container {
  position: absolute;
  top: 0;
  left: 1;
  right: 1;
  z-index: 999;
  max-height: 100%;
  overflow-y: auto;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-button {
  padding: 6px 12px;
  margin: 0 4px;
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
}

.pagination-button.active {
  background-color: #23b78d;
  color: #fff;
  border-color: #23b78d;
}
</style>
