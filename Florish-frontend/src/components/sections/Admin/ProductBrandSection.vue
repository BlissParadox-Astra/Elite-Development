<template>
  <v-main>
    <v-container class="mt-5">
      <v-row justify="center">
        <v-col cols="12">
          <h2 class="text-center">BRAND</h2>
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <v-row>
        <v-col cols="12" sm="9">
          <SearchField />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block @click="showForm = true">Add BRAND</v-btn>
        </v-col>
        <ProductClassification v-if="showForm" title="Brand Module" :input-label="brandInputLabel"
          :product="editingProduct" :product-index="editingProductIndex" @brand-edited="handleBrandEdited"
          @brand-added="handleBrandAdded" @cancel="cancelBrandAdd" />
      </v-row>
      <v-row>
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="products" :showEditIcon="true" :showDeleteIcon="true"
            @edit-data="editProductRow" @delete-data="deleteProductRow" height="500px" />
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
        { key: "brand", label: "BRAND" },
      ],
      products: [
        { brand: "COCA COLA" },
        { brand: "Colgate" },
      ],
      brandInputLabel: "Brand Name",
    };
  },

  methods: {
    handleBrandAdded(newBrand) {
      if (this.editingProductIndex !== -1) {
        this.products[this.editingProductIndex].brand = newBrand;
        this.editingProduct = null;
        this.editingProductIndex = -1;
      } else {
        this.products.push({ brand: newBrand });
      }
      this.showForm = false;
    },

    handleBrandEdited(newBrand, index) {
      if (index !== -1) {
        this.products[index].brand = newBrand;
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
      const index = this.products.findIndex(p => p.brand === product.brand);
      this.editingProductIndex = index;
      this.showForm = true;
    },

    deleteProductRow(product) {
      const index = this.products.findIndex(p => p.brand === product.brand);
      if (index !== -1) {
        this.products.splice(index, 1);
      }
    },
  },
};
</script>
  