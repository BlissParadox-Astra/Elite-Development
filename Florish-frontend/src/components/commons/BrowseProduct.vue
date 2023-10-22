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
        <v-data-table-server v-model:items-per-page="itemsPerPage" :page="page" :headers="headers"
          :items-length="totalItems" :items="products" :loading="loading" item-value="id" class="elevation-1"
          @update:options="getProducts">
          <template v-slot:custom-sort="{ header }">
            <span v-if="header.key === 'actions'">Actions</span>
          </template>
          <template v-slot:item="{ item }">
            <tr>
              <td>{{ item.id }}</td>
              <td>{{ item.product_code }}</td>
              <td>{{ item.barcode }}</td>
              <td>{{ item.description }}</td>
              <td>{{ item.stock_on_hand }}</td>
              <td>
                <span>
                  <v-icon @click="addToCartProduct(item)">mdi-cart-plus</v-icon>
                </span>
              </td>
            </tr>
          </template>
        </v-data-table-server>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import SearchField from "../commons/SearchField.vue";
import axios from 'axios';

export default {
  components: {
    SearchField,
  },

  data() {
    return {
      loading: true,
      itemsPerPage: 10,
      page: 1,
      id: 1,
      products: [],
      totalItems: 0,
      headers: [
        { title: '#', key: 'id' },
        { title: 'Product Code', key: 'product_code' },
        { title: 'Barcode', key: 'barcode' },
        { title: 'Description', key: 'description' },
        { title: 'Stock On Hand', key: "stock_on_hand" },
        { title: 'Actions', key: 'actions', sortable: false }
      ],
      showProductForm: false,
    };
  },

  async mounted() {
    await this.getProducts();
  },

  props: {
    addToCart: Function,
  },

  methods: {
    getProducts() {
      axios
        .get('/products', {
          params: {
            page: this.page,
            itemsPerPage: this.itemsPerPage,
          }
        }).then((res) => {
          this.products = [...res.data.products.data];
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching products:', error);
        });
  },

  addToCartProduct(product) {
    window.alert("Product added to cart!");
    this.addToCart(product);
  },

  closeForm() {
    this.showProductForm = false;
    this.$emit("close");
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
