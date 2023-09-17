<template>
  <v-main>
    <v-container>
      <v-row>
        <v-col cols="12" sm="9">
          <SearchField />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block @click="showBrandForm">Add Brand</v-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="brands" :showEditIcon="true" :showDeleteIcon="true"
            @edit-data="editBrandRow" @delete-data="showDeleteConfirmation" class="custom-table" />
        </v-col>
      </v-row>
      <DeleteConfirmationDialog @confirm-delete="deleteBrand" ref="deleteConfirmationDialog" />
      <v-row>
        <v-col cols="12">
          <v-row class="d-flex justify-center">
            <v-col cols="12" sm="5" xl="5" lg="5" md="5" class="mt-5 form-container">
              <BrandForm v-if="showForm" @add="addBrand" :existingCategories="existingCategories"
                @update="updateBrand(editingBrandIndex, $event)" @cancel="hideBrandForm" :initialBrand="editingBrand" />
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
import BrandForm from "../../common/BrandForm.vue";
import DeleteConfirmationDialog from '../../common/DeleteConfirmationDialog.vue';
import axios from 'axios';

export default {
  name: "ProductBrandSection",
  components: {
    CustomTable,
    SearchField,
    BrandForm,
    DeleteConfirmationDialog,
  },

  data() {
    return {
      showForm: false,
      brands: [],
      editingBrand: null,
      editingBrandIndex: -1,
      tableColumns: [
        { key: "brand_name", label: "Brand Name" },
      ],
      existingCategories: [],
      loadingCategories: false,
    };
  },

  async mounted() {
    this.loadingCategories = true;
    await this.fetchCategories();
    this.loadingCategories = false;
    await this.getBrands();
  },

  methods: {
    getBrands() {
      axios.get('/brands').then(res => {
        this.brands = res.data.brands.map(brand => ({
          ...brand,
          category_name: brand.category.category_name,
        }))
      });
    },

    addBrand(brandData) {
      if (!brandData.brand_name || !brandData.categoryName) {
        return;
      }
      this.brands.push(brandData);
      this.hideBrandForm();
    },

    editBrandRow(brand) {
      const category = this.existingCategories.find(category => category.category_name === brand.category.category_name);

      if (category) {
        this.editingBrand = {
          ...brand,
          category_id: category.id,
        };

        const index = this.brands.findIndex(p => p.id === brand.id);
        this.editingBrandIndex = index;
        this.showForm = true;
      } else {
        console.error(`Category "${brand.category.category_name}" not found.`);
      }
    },

    async fetchCategories() {
      try {
        const response = await axios.get('/categories');
        this.existingCategories = response.data.categories;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    updateBrand(index, updatedBrand) {
      this.brands[index] = updatedBrand;
      this.editingBrand = null;
      this.hideBrandForm();
    },

    deleteBrand() {
      if (this.itemToDelete) {
        axios.delete(`/brand/${this.itemToDelete.id}`)
          .then(() => {
            const index = this.brands.findIndex(brand => brand.id === this.itemToDelete.id);
            if (index !== -1) {
              this.brands.splice(index, 1);
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

    showBrandForm() {
      this.showForm = true;
    },

    hideBrandForm() {
      this.showForm = false;
      this.editingBrand = null;
      this.editingBrandIndex = -1;
    },

    cancelBrandAdd() {
      this.showForm = false;
      this.editingBrand = null;
      this.editingBrandndex = -1;
    },

    resetFormFields() {
      this.brand_name = '';
    },
    clearErrors() {
      this.brandError = '';
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
  