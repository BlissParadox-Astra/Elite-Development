<template>
  <v-container class="mt-15 showProductForm">
    <v-row>
      <v-col cols="12">
        <div @click="cancelForm" class="close-button">
          <v-icon color="white">mdi-close</v-icon>
        </div>
        <v-form @submit.prevent="submitForm" class="form">
          <v-row  justify="center" class="bg-teal pa-3">
            <h2 class="text-center">
            {{ editingProduct ? 'Edit Product' : 'Product Module' }}
          </h2>
          </v-row>
         
          <v-row justify="center" class="bg-teal-darken-2 pa-2">
            <v-col cols="12" md="6">
              <v-text-field v-model="barcode" label="Bar Code" placeholder="Enter BarCode" :error-messages="barCodeError"></v-text-field>

            </v-col>
            <v-col cols="12" md="6">
              <v-text-field v-model="description" label="Description" placeholder="Enter Description"
                @input="clearFieldErrors('description')" :error-messages="descriptionError" :rules="[v => !!v || 'Description is required']"></v-text-field>

            </v-col>
            <v-col cols="12" md="6">
              <v-select v-model="category_name"
                :items="existingCategories.length > 0 ? existingCategories.map(category => category.category_name) : []"
                label="Categories" placeholder="Choose Category" :error-messages="selectedCategoryError"
                @input="clearFieldErrors('categories')"></v-select>
            </v-col>
            <v-col cols="12" md="6">
              <v-select v-model="brand_name" label="Brand"
                :items="existingBrands.length > 0 ? existingBrands.map(brand => brand.brand_name) : []"
                placeholder="Enter Brand Name" @input="clearFieldErrors('brands')"
                :error-messages="brandError"  :rules="[v => !!v || 'Brand is required']" autocomplete></v-select>

            </v-col>
            <v-col cols="12" md="6">
              <v-text-field v-model="price" label="Price" placeholder="Enter Price" @input="clearFieldErrors('price')"
                :error-messages="priceError" :rules="[v => !!v || 'Price is required']"></v-text-field>

            </v-col>
            <v-col cols="12" md="6">
              <v-text-field v-model="reorder_level" label="Reorder Level" placeholder="Enter Reorder Level" @input="clearFieldErrors('reorderLevel')"
                :error-messages="reorderLevelError" :rules="[v => !!v || 'Reorder level is required']"></v-text-field>

            </v-col>
          </v-row>
          <v-row  justify="center" class="bg-teal-darken-1 pa-2">
            <v-col cols="12" md="4" lg="4" sm="4">
              <v-btn type="submit" color="#23b78d" block>
                {{ editingProduct ? "Save" : "Submit" }}
              </v-btn>
            </v-col>
            <v-col cols="12" md="4" lg="4" sm="4">
              <v-btn type="button" color="#068863" block @click="cancelForm">Cancel</v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'ProductForm',
  props: ['initialProduct', 'existingCategories', 'existingBrands'],
  data() {
    return {
      barcode: this.initialProduct ? this.initialProduct.barcode : "",
      description: this.initialProduct ? this.initialProduct.description : "",
      category_name: this.initialProduct ? this.initialProduct.category_name : "",
      brand_name: this.initialProduct ? this.initialProduct.brand_name : "",
      price: this.initialProduct ? this.initialProduct.price : "",
      reorder_level: this.initialProduct ? this.initialProduct.reorder_level : "",
      stockOnHand: this.initialProduct ? this.initialProduct.stockOnHand : 0,
      editingProduct: !!this.initialProduct,

      barCodeError: "",
      descriptionError: "",
      brandError: "",
      selectedCategoryError: '',
      priceError: "",
      reorderLevelError: "",
    };
  },

  methods: {
    async submitForm() {
      this.clearErrors();
      const categoryId = this.findCategoryIdByName(this.category_name);
      const brandId = this.findBrandIdByName(this.brand_name);
      if (!categoryId) {
        this.selectedCategoryError = 'Category is required'
      }
      else if (!brandId) {
        this.brandError = 'Brand is required.';
      }
      else if (
        this.barCodeError ||
        this.descriptionError ||
        this.brandError ||
        this.selectedCategoryError ||
        this.priceError ||
        this.reorderLevelError
      ) {
        return;
      }
      const productData = {
        id: this.initialProduct ? this.initialProduct.id : null,
        barcode: this.barcode,
        description: this.description,
        category_id: categoryId,
        brand_id: brandId,
        price: this.price,
        reorder_level: this.reorder_level,
        stockOnHand: this.stockOnHand,
      };
      if (this.editingProduct) {
        this.$emit('update-product', productData);
      } else {
        this.$emit('add-product', productData);
      }
    },

    findCategoryIdByName(categoryName) {
      const category = this.existingCategories.find(category => category.category_name === categoryName);
      return category ? category.id : null;
    },

    findBrandIdByName(brandName) {
      const brand = this.existingBrands.find(brand => brand.brand_name === brandName);
      return brand ? brand.id : null;
    },

    resetFormFields() {
      this.barcode = "";
      this.description = "";
      this.category_name = "";
      this.brand_name = "";
      this.price = "";
      this.reorder_level = "";
      this.stockOnHand = 0;
    },

    clearErrors() {
      this.barCodeError = "";
      this.descriptionError = "";
      this.brandError = "";
      this.selectedCategoryError = '';
      this.priceError = "";
      this.reorderLevelError = "";
    },

    clearFieldErrors(fieldName) {
      this[fieldName + 'Error'] = '';
    },

    cancelForm() {
      this.resetFormFields();
      this.editingProduct = false;
      this.$emit("cancel");
    },
  },
};
</script>

<style scoped>
.showProductForm {
  /* background-image: url("../../assets/assets/vuejs.jpg"); */
  /* background-color: #23b78d; */
  z-index: 999;
}


.close-button {
  position: absolute;
  top: 90px;
  right: 35px;
  z-index: 999;
  font-size: larger;
}


.error-messages {
  color: red;
}
</style>
