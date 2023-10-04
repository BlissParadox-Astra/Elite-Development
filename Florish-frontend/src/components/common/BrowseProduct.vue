<template>
  <v-container class="browseProduct">
    <v-row>
      <v-col>
        <h2 class="text-center mb-1">Product Module</h2>
      </v-col>
      <v-btn icon @click="closeForm" class="close-button" color="transparent">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-row>
    <v-row>
      <v-col cols="12" sm="9">
        <SearchField />
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12">
        <CustomTable :columns="tableColumns" :items="products" :showAddToCartIcon="true"
          @add-to-cart-product="addToCartProduct" class="custom-table" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import SearchField from "@/components/common/SearchField.vue";
import CustomTable from "./CustomTable.vue";
import axios from 'axios';

export default {
  components: {
    SearchField,
    CustomTable,
  },

  data() {
    return {
      products: [],
      tableColumns: [
        { key: "product_code", label: "Product Code" },
        { key: "barcode", label: "Barcode" },
        { key: "description", label: "Description" },
        { key: "stock_on_hand", label: "Stock On Hand" },
      ],
      showProductForm: false,
    };
  },

  mounted() {
    this.getProducts();
  },

  props: {
    addToCart: Function,
  },

  methods: {
    getProducts() {
      axios.get('/products').then(res => {
        this.products = res.data.products
      });
    },

    closeForm() {
      this.showProductForm = false;
      this.$emit("close");
    },

    addToCartProduct(product) {
      window.alert("Product added to cart!");
      this.addToCart(product);
    },
  },
};
</script>

<style scoped>
.browseProduct {
  background-image: url("../../assets/assets/vuejs.jpg");
  z-index: 999;
}


.custom-table {
  height: 445px;
}


.close-button {
  position: absolute;
  top: 25px;
  right: 20px;
  z-index: 999;
}
</style>
