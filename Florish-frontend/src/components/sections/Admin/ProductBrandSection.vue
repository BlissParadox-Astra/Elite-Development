<template>
  <v-container class="mt-14">
    <v-row>
      <v-col cols="12" sm="9">
        <SearchField @search="handleSearch" :searchLabel="searchLabel" :searchType="'regular'" />
      </v-col>
      <v-col cols="12" sm="3" class="d-flex justify-center align-center">
        <v-btn color="#23b78d" block @click="showBrandForm">Add Brand</v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12">
        <v-data-table :headers="headers" :items="brands" :loading="loading" :page="currentPage"
          :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
          @update:options="debouncedGetBrands" fixed-header height="400">
          <template v-slot:custom-sort="{ header }">
            <span v-if="header.key === 'actions'">Actions</span>
          </template>
          <template v-slot:item="{ item, index }">
            <tr>
              <td>{{ displayedIndex + index }}</td>
              <td>{{ item.brand_name }}</td>
              <td>
                <span>
                  <v-icon @click="editBrandRow(item)" color="primary">mdi-pencil</v-icon>
                </span>
                <span style="margin-left: 15px;">
                  <v-icon @click="showDeleteConfirmation(item)" color="error">mdi-delete</v-icon>
                </span>
              </td>
            </tr>
          </template>
          <template v-slot:bottom>
            <div class="text-center pt-8 pagination">
              <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                :disabled="currentPage === 1">Previous</v-btn>

              <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                :class="{ active: pageNumber === currentPage }" class="pagination-button">
                {{ pageNumber }}
              </v-btn>

              <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                :disabled="currentPage === totalPages">Next</v-btn>
            </div>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <DeleteConfirmationDialog @confirm-delete="deleteBrand" ref="deleteConfirmationDialog" />
    <v-row>
      <v-col cols="12">
        <v-row class="d-flex justify-center">
          <v-col cols="12" sm="5" xl="5" lg="5" md="5" class="mt-5 form-container">
            <BrandForm v-if="showForm" @add-brand="addBrand" :existingCategories="existingCategories"
              @update-brand="updateBrand" @cancel="hideBrandForm" :initialBrand="editingBrand" />
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
import BrandForm from "../../forms/BrandForm.vue";
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
import _debounce from 'lodash/debounce';
import axios from 'axios';

export default {
  name: "ProductBrandSection",
  components: {
    SearchField,
    BrandForm,
    DeleteConfirmationDialog,
  },

  data() {
    return {
      showForm: false,
      itemsPerPage: 10,
      totalItems: 0,
      currentPage: 1,
      brands: [],
      editingBrand: null,
      editingBrandIndex: -1,
      loading: true,
      id: 1,
      snackbar: false,
      snackbarColor: '',
      searchQuery: '',
      searchLabel: 'Search Brand Here',
      headers: [
        { title: '#', value: 'index' },
        { title: 'Brand Name', key: "brand_name" },
        { title: 'Actions', key: 'actions', sortable: false }
      ],
      existingCategories: [],
      loadingCategories: false,
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
      const maxVisiblePages = 5;
      const halfMaxVisiblePages = Math.floor(maxVisiblePages / 2);
      const firstPage = Math.max(1, this.currentPage - halfMaxVisiblePages);
      const lastPage = Math.min(this.totalPages, firstPage + maxVisiblePages - 1);

      const range = [];
      for (let i = firstPage; i <= lastPage; i++) {
        range.push(i);
      }

      return range;
    },
  },

  async mounted() {
    this.loadingCategories = true;
    await this.fetchCategories();
    this.loadingCategories = false;
    await this.debouncedGetBrands();
  },

  methods: {
    debouncedGetBrands: _debounce(function () {
      this.getBrands();
    }, 1000),

    handleSearch(query) {
      this.searchQuery = query;
      this.currentPage = 1;
      this.debouncedGetBrands();
    },

    getBrands() {
      this.loading = true;
      axios
        .get('/brands', {
          params: {
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
            search: this.searchQuery,
          }
        })
        .then((res) => {
          this.brands = res.data.brands;
          this.categories = res.data.categories;
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching brands:', error);
        });
    },

    async fetchCategories() {
      try {
        const response = await axios.get('/get-categories');
        this.existingCategories = response.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    previousPage() {
      this.loading = true;
      if (this.currentPage > 1) {
        this.currentPage--;
        this.debouncedGetBrands();
      }
    },

    nextPage() {
      this.loading = true;
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.debouncedGetBrands();
      }
    },

    gotoPage(pageNumber) {
      this.loading = true;
      this.currentPage = pageNumber;
      this.debouncedGetBrands();
    },

    async addBrand(brandData) {
      try {
        const response = await axios.post('/brand', brandData);
        if (response.status === 200) {
          this.brands.push(response.data);
          this.hideBrandForm();
          this.snackbarColor = 'success';
          this.showSnackbar(response.data.message, 'success');
          this.getBrands();
        } else {
          this.snackbarColor = 'error';
          this.showSnackbar(response.data.message, 'error');
        }
      } catch (error) {
        console.error('Error adding brand:', error);
        this.snackbarColor = 'error';
        this.showSnackbar(error.response.data.message, 'error');
      }
    },

    editBrandRow(brand) {
      const category = this.existingCategories.find(category => category.category_name === brand.category.category_name);
      if (category) {
        this.editingBrand = {
          ...brand,
          category_name: brand.category.category_name,
          id: brand.id,
        };

        const index = this.brands.findIndex(p => p.id === brand.id);
        this.editingBrandIndex = index;
        this.showForm = true;
      } else {
        this.editingBrand = {
          ...brand,
          category_name: '',
          id: brand.id,
        };

        const index = this.brands.findIndex(p => p.id === brand.id);
        this.editingBrandIndex = index;
        this.showForm = true;
        console.error(`Category "${brand.category.category_name}" not found.`);
      }
    },

    async updateBrand(brandData) {
      try {
        const response = await axios.put(`/brand/${brandData.id}`, brandData);
        if (response.status === 200) {
          this.brands[this.index] = brandData;
          this.hideBrandForm();
          this.snackbarColor = 'success';
          this.showSnackbar(response.data.message, 'success');
          this.editingBrand = null;
          this.getBrands();
        } else {
          this.snackbarColor = 'error';
          this.showSnackbar(response.data.message, 'error');
        }
      } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 422) {
          const validationErrors = error.response.data.errors;
          const errorMessage = Object.values(validationErrors).flat()[0] || 'An error occurred';
          this.snackbarColor = 'error';
          this.showSnackbar(errorMessage, 'error');
        } else {
          this.snackbarText = error.response.data.error;
          this.snackbarColor = 'error';
          this.showSnackbar(this.snackbarText, 'error');
        }
      }
    },

    async deleteBrand() {
      if (this.itemToDelete) {
        axios.delete(`/brand/${this.itemToDelete.id}`)
          .then((response) => {
            const index = this.brands.findIndex(brand => brand.id === this.itemToDelete.id);
            if (index !== -1) {
              this.brands.splice(index, 1);
            }
            this.$refs.deleteConfirmationDialog.closeDialog();
            this.showSnackbar(response.data.message, 'success');
            this.getBrands();
          })
          .catch((error) => {
            if (error.response.data.error === 'Cannot delete brand. In use by other records.') {
              this.showSnackbar('Cannot delete brand. In use by other records.', 'error');
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

    showBrandForm() {
      this.showForm = true;
    },

    hideBrandForm() {
      this.showForm = false;
      this.editingBrand = null;
      this.editingBrandIndex = -1;
      this.getBrands();
    },

    cancelBrandAdd() {
      this.showForm = false;
      this.editingBrand = null;
      this.editingBrandndex = -1;
      this.getBrands();
    },

    resetFormFields() {
      this.brand_name = '';
    },

    clearErrors() {
      this.brandError = '';
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

.custom-table {
  height: 500px;
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
  background-color: #007bff;
  color: #fff;
  border-color: #007bff;
}
</style>
  