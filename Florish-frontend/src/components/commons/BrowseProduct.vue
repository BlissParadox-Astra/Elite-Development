<template>
  <v-container class="browseProduct mt-14">
    <v-row>
      <v-col>
        <div @click="closeForm" class="close-button">
          <v-icon color="white">mdi-close</v-icon>
        </div>
        <v-row justify="center" class="bg-teal pa-3">
          <h2 class="text-center">Product Module</h2>
        </v-row>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" sm="9">
        <SearchField @search="handleSearch" :searchType="'regular'" />
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12">
        <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
          :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
          @update:options="debouncedGetProducts" fixed-header height="400">
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
            <div class="text-center pt-6 pagination">
              <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                :disabled="currentPage === 1"><v-icon>mdi-chevron-left</v-icon> Prev</v-btn>

              <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                :class="{ active: pageNumber === currentPage }" class="pagination-button">
                {{ pageNumber }}
              </v-btn>

              <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                :disabled="currentPage === totalPages">Next
                <v-icon>mdi-chevron-right</v-icon></v-btn>
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
import _debounce from 'lodash/debounce';
import axios from 'axios';

export default {
  components: {
    SearchField,
  },

  props: {
    addToCart: Function,
    context: String,
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
      searchQuery: '',
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
    await this.debouncedGetProducts();
  },

  methods: {
    debouncedGetProducts: _debounce(function () {
      this.getProducts();
    }, 1000),

    handleSearch(query) {
      this.searchQuery = query;
      this.currentPage = 1;
      this.debouncedGetProducts();
    },

    getProducts() {
      this.loading = true;
      if (this.context === 'transaction') {
        axios.get('/products', {
          params: {
            context: 'transaction',
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
            search: this.searchQuery,
          }
        })
          .then((res) => {
            this.products = res.data.products;
            this.totalItems = res.data.totalItems;
            this.loading = false;
          })
          .catch((error) => {
            console.error('Error fetching products:', error);
          });
      } else if (this.context === 'stockIn') {
        axios.get('/products', {
          params: {
            context: 'stockIn',
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
            search: this.searchQuery,
          }
        })
          .then((res) => {
            this.products = res.data.products;
            this.totalItems = res.data.totalItems;
            this.loading = false;
          })
          .catch((error) => {
            console.error('Error fetching products:', error);
          });
      } else {
        axios
          .get('/products', {
            params: {
              page: this.currentPage,
              itemsPerPage: this.itemsPerPage,
              search: this.searchQuery,
            }
          }).then((res) => {
            this.products = res.data.products;
            this.totalItems = res.data.totalItems;
            this.loading = false;
          })
          .catch((error) => {
            console.error('Error fetching products:', error);
          });
      }
    },

    previousPage() {
      this.loading = true;
      if (this.currentPage > 1) {
        this.currentPage--;
        this.debouncedGetProducts();
      }
    },

    nextPage() {
      this.loading = true;
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.debouncedGetProducts();
      }
    },

    gotoPage(pageNumber) {
      this.loading = true;
      this.currentPage = pageNumber;
      this.debouncedGetProducts();
    },

    addToCartProduct(product) {
      if (this.isAddingToCart) {
        return;
      }

      this.isAddingToCart = true;

      const addToCartSuccess = this.addToCart(product);

      if (addToCartSuccess) {
        this.snackbarColor = 'success';
        this.showSnackbar('Product successfully added to cart', 'success');
      }

      setTimeout(() => {
        this.isAddingToCart = false;
      }, 1000);
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
  background-color: rgba(233, 224, 224, 0.949);
  z-index: 999;
}

.close-button {
  position: absolute;
  top: 90px;
  right: 30px;
  z-index: 999;
  font-size: larger;
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
  background-color: #23b78d;
  color: #fff;
  border-color: #23b78d;
}
</style>
