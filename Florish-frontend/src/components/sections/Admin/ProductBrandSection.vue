<template>
  <v-main>
    <v-container>
      <v-row>
        <v-col cols="12" sm="9">
          <SearchField />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block @click="showForm = true">Add BRAND</v-btn>
        </v-col>

      </v-row>
      <v-row>
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="brands" :showEditIcon="true" :showDeleteIcon="true"
            @edit-data="editProductRow" @delete-data="deleteProductRow" height="500px" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-row class="d-flex justify-center">
            <v-col cols="12" sm="5" xl="5" lg="5" md="5" class="mt-5 form-container">
              <ProductClassification v-if="showForm" title="Brand Module" :input-label="brandInputLabel"
                :product="editingProduct" :product-index="editingProductIndex" :existingCategories="existingCategories"
                @brand-edited="handleBrandEdited" @add-brand="handleBrandAdded" @cancel="cancelBrandAdd" />
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
  name: "ProductBrandSection",
  components: {
    CustomTable,
    SearchField,
    ProductClassification,
  },

  data() {
    return {
      showForm: false,
      editingProduct: null,
      editingProductIndex: -1,
      tableColumns: [
        { key: "brand_name", label: "BRAND" },
      ],
      brands: [],
      brandInputLabel: "Brand Name",
      existingCategories: []
    };
  },

  mounted() {
    this.getBrands();
    this.fetchCategories();
  },

  methods: {
    getBrands() {
      axios.get('/brands').then(res => {
        this.brands = res.data.brands
      });
    },

    handleBrandAdded(newProduct, categoryId) {
      const brandData = {
        brand_name: newProduct,
        category_id: categoryId,
      };
      axios.post('/brand', brandData)
        .then(response => {
          this.brands.push(response.data);
          alert(response.data.message);
          this.reloadPage();
        })
        .catch(error => {
          if (error.response) {
            console.error('Server responded with errors:', error.response.data);
          } else if (error.request) {
            console.error('Request made but no response received:', error.request);
          } else {
            console.error('Error setting up the request:', error.message);
          }
        });
      this.showForm = false;
    },

    async fetchCategories() {
      try {
        const response = await axios.get('/get-categories'); // Replace with your actual API endpoint
        this.existingCategories = response.data; // Store user types in the variable
      } catch (error) {
        console.error(error);
      }
    },

    handleBrandEdited(newBrand, index) {
      if (index !== -1) {
        this.brands[index].brand = newBrand;
      }
      this.showForm = false;
      this.editingProduct = null;
      this.editingProductIndex = -1;
    },

    cancelBrandAdd() {
      this.showForm = false;
      this.editingProduct = null;  // Reset editingProduct
      this.editingProductIndex = -1; // Reset editingProductIndex
    },

    editProductRow(product) {
      this.editingProduct = { ...product };
      const index = this.brands.findIndex(p => p.brand === product.brand);
      this.editingProductIndex = index;
      this.showForm = true;
    },

    deleteProductRow(product) {
      const index = this.brands.findIndex(p => p.brand === product.brand);
      if (index !== -1) {
        this.brands.splice(index, 1);
      }
    },
    reloadPage() {
      window.location.reload();
    },
  },
};
</script>
  