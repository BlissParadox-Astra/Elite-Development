<template>
  <v-container class="mt-2">
    <v-row>
      <v-col>
        <h2 class="text-center mb-1">Product Module</h2>
      </v-col>

      <v-btn icon @click="closeForm" class="close-icon text-right">
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
          @add-to-cart-product="addToCartProduct" height="390px" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import SearchField from "../commons/SearchField.vue";
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

  methods: {
    getProducts() { 
            axios.get('/products').then(res => {
                this.products = res.data.products
                // console.log(this.users)
            });
        },

    closeForm() {
      this.showProductForm = false;
      this.$emit("close");
    },

    addToCartProduct(product) {
      this.$emit("add-to-cart-product", product);
    },
  },
};
</script>

<style scoped>
.mt-2 {
  background-color: #72A568E5;
}

.close-icon {
  top: 10px;
  right: 10px;
  color: #080000;
}
</style>
