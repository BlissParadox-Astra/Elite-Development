<template>
  <v-main>
    <v-container class="mt-5">
      <v-row>
        <v-col cols="12" sm="9">
          <SearchField />
        </v-col>
      </v-row>
      <v-row justify="center">
        <v-col cols="12">
          <CustomTable v-if="products.length > 0" :columns="tableColumns" :items="products" :showFetchIcon="true" @fetch-data="fetchProduct"
            height="480px" />
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <v-row>
        <v-col>
          <v-text-field label="Reference No." placeholder="REF NO." readonly />
        </v-col>
        <v-col>
          <v-select v-model="Options" :items="commandOptions"></v-select>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="6">
          <v-text-field v-model="selectedRow.product_code" label="Product Code" placeholder="PRODUCT CODE" readonly />
        </v-col>
        <v-col cols="6">
          <v-text-field v-model="selectedRow.barcode" label="Bar Code" placeholder="BAR CODE" readonly />
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-text-field v-model="selectedRow.description" label="Description" placeholder="DESCRIPTION" readonly />
        </v-col>
        <v-col>
          <v-text-field label="Remarks" placeholder="Enter REMARKS" />
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-text-field label="Quantity" placeholder="Enter QUANTITY" />
        </v-col>
        <v-col>
          <v-text-field label="User" placeholder="USER" />
        </v-col>
      </v-row>
      <v-row class="mt-3">
        <v-col cols="2" offset-md="10">
          <v-btn color="success" style="width: 150px">Save</v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
  
<script>
import SearchField from "../../common/SearchField.vue";
import CustomTable from "../../common/CustomTable.vue";
import axios from 'axios'

export default {
  name: "StockAdjustment",
  components: {
    SearchField,
    CustomTable,
  },
  data() {
    return {
      products: [],
      selectedRow: {
        product_code: "",
        barcode: "",
        description: "",
      },

      tableColumns: [
        { key: "product_code", label: "Product Code" },
        { key: "barcode", label: "Barcode" },
        { key: "description", label: "Description" },
        { key: "price", label: "Price" },
        { key: 'brand', label: 'Brand', render: this.renderProductBrand },
        { key: 'category', label: 'Category', render: this.renderProductCategory },
        { key: 'stock_on_hand', label: 'Stock On Hand' },
      ],

      commandOptions: ["Remove From Inventory", "Add to Inventory"],

      Options: "Select an Actions",
    };
  },

  mounted() {
    this.getProducts();
  },

  methods: {
    getProducts() {
      axios.get('/products').then(res => {
        this.products = res.data.products
        console.log(this.products)
      });
    },

    renderProductCategory(category) {
      return category.category ? category.category.category_name : 'Unknown';
    },

    renderProductBrand(brand) {
      return brand.brand ? brand.brand.brand_name : 'Unknown';
    },

    fetchProduct(product) {
      // this.selectedRow.referenceNo = product.referenceNo;
      this.selectedRow.product_code = product.product_code;
      this.selectedRow.barcode = product.barcode;
      this.selectedRow.description = product.description;
    },
  },
};
</script>
  