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
        <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
          :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
          @update:options="getProducts" fixed-header height="400">
          <template v-slot:custom-sort="{ header }">
            <span v-if="header.key === 'actions'">Actions</span>
          </template>
          <template v-slot:item="{ item, index }">
            <tr>
              <td>{{ displayedIndex + index }}</td>
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
          <template v-slot:bottom>
            <div class="text-center pt-2">
              <button @click="previousPage" :disabled="currentPage === 1">Previous</button>

              <button v-for="pageNumber in totalPages" :key="pageNumber" @click="gotoPage(pageNumber)"
                :class="{ active: pageNumber === currentPage }">
                {{ pageNumber }}
              </button>

              <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
            </div>
          </template>
        </v-data-table>
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
      currentPage: 1,
      id: 1,
      products: [],
      totalItems: 0,
      snackbar: false,
      snackbarColor: '',
      headers: [
        { title: '#', value: 'index' },
        { title: 'Product Code', key: 'product_code' },
        { title: 'Barcode', key: 'barcode' },
        { title: 'Description', key: 'description' },
        { title: 'Stock On Hand', key: "stock_on_hand" },
        { title: 'Actions', key: 'actions', sortable: false }
      ],
      showProductForm: false,
    };
  },

  computed: {
    displayedIndex() {
      return (this.currentPage - 1) * this.itemsPerPage + 1;
    },
    totalPages() {
      return Math.ceil(this.totalItems / this.itemsPerPage);
    },
  },

  async mounted() {
    await this.getProducts();
  },

  props: {
    addToCart: Function,
  },

  methods: {
    getProducts() {
      this.loading = true;
      axios
        .get('/products', {
          params: {
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
          }
        }).then((res) => {
          this.products = res.data.products;
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching products:', error);
        });
    },

    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.getProducts();
      }
    },

    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.getProducts();
      }
    },

    gotoPage(pageNumber) {
      this.currentPage = pageNumber;
      this.getProducts();
    },

    addToCartProduct(product) {
      this.snackbarColor = 'success';
      this.showSnackbar('Product successfully added to cart', 'success');
      this.addToCart(product);
    },

    closeForm() {
      this.showProductForm = false;
      this.$emit("close");
    },

    showSnackbar(text, color, timeout = 1000) {
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
