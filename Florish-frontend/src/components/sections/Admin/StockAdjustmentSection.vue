<template>
  <v-main>
    <v-container class="mt-15">
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
                <td>{{ item.brand.brand_name }}</td>
                <td>{{ item.category.category_name }}</td>
                <td>{{ item.price }}</td>
                <td>{{ item.reorder_level }}</td>
                <td>{{ item.stock_on_hand }}</td>
                <td>
                  <span>
                    <v-icon @click="fetchProduct(item)" color="#23b78d">mdi-arrow-right</v-icon>
                  </span>
                </td>
              </tr>
            </template>
            <template v-slot:bottom>
              <v-col cols="12">
                <div v-if="totalPages > 1" class="text-center pt-5 pagination">
                  <v-btn :disabled="currentPage === 1" class="pagination-button" @click="previousPage" color="#23b78d">
                    <v-icon>mdi-chevron-left</v-icon> Prev
                  </v-btn>

                  <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                    :class="{ active: pageNumber === currentPage }" class="pagination-button">
                    {{ pageNumber }}
                  </v-btn>

                  <v-btn :disabled="currentPage === totalPages" class="pagination-button" @click="nextPage"
                    color="#23b78d">
                    Next <v-icon>mdi-chevron-right</v-icon>
                  </v-btn>
                </div>
                <div v-else class="text-center pt-5">
                  <v-btn @click="gotoPage(1)" :class="{ active: 1 === currentPage }" class="pagination-button">
                    1
                  </v-btn>
                </div>
              </v-col>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <input type="hidden" v-model="selected_product_id" />
      <v-row>
        <v-col>
          <v-text-field v-model="reference_number" label="Reference No." placeholder="Reference No." readonly />
        </v-col>
        <v-col>
          <v-select v-model="options" :items="commandOptions"></v-select>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="6">
          <v-text-field v-model="selectedRow.product_code" label="Product Code" placeholder="Product Code" readonly />
        </v-col>
        <v-col cols="6">
          <v-text-field v-model="selectedRow.barcode" label="Bar Code" placeholder="Bar Code" readonly />
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-text-field v-model="selectedRow.description" label="Description" placeholder="Description" readonly />
        </v-col>
        <v-col>
          <v-text-field v-model="remarks" label="Remarks" placeholder="Enter Remarks" />
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-text-field v-model="quantity" label="Quantity" placeholder="Enter Quantity" @input="validateQuantity"
            @keypress="filterNumeric" />
          <div v-if="quantityError" class="text-error">{{ quantityError }}</div>
        </v-col>
        <v-col>
          <v-text-field v-model="user" label="User" placeholder="USER" :model-value="stock_in_by" readonly />
        </v-col>
      </v-row>
      <v-row class="mt-3">
        <v-col cols="2" offset-md="10">
          <v-btn @click="showConfirmation" :disabled="isSaveButtonDisabled" color="#23b78d"
            style="width: 150px">Save</v-btn>
        </v-col>
      </v-row>
      <v-dialog v-model="showConfirmationDialog" max-width="400" class="center-dialog  no-background">
        <v-card>
          <v-card-title class="d-flex justify-center bg-teal pa-3">
            Confirm Save
          </v-card-title>
          <v-card-text class="text-center">
            ARE YOU SURE YOU WANT TO SAVE THIS STOCK ADJUSTMENT RECORDS?
          </v-card-text>
          <v-card-actions class="d-flex justify-center ">
            <div>
              <v-btn color="#23b78d" @click="saveStockAdjustment" style="width: 150px;">Yes</v-btn>
              <v-btn @click="cancelSave" color="#068863">No</v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="snackbar" right top :color="snackbarColor">
        {{ snackbarText }}
        <template v-slot:actions>
          <v-btn color="pink" variant="text" @click="snackbar = false">
            Close
          </v-btn>
        </template>
      </v-snackbar>
    </v-container>
  </v-main>
</template>
  
<script>
import SearchField from "../../commons/SearchField.vue";
import { mapState } from 'vuex';
import _debounce from 'lodash/debounce';
import axios from 'axios'

export default {
  name: "StockAdjustment",

  components: {
    SearchField,
  },

  data() {
    return {
      itemsPerPage: 10,
      currentPage: 1,
      id: 1,
      totalItems: 0,
      loading: true,
      reference_number: '',
      quantity: '',
      remarks: '',
      quantityError: null,
      selected_product_id: null,
      showConfirmationDialog: false,
      products: [],
      snackbar: false,
      snackbarColor: '',
      searchQuery: '',
      selectedRow: {
        product_code: "",
        barcode: "",
        description: "",
      },
      headers: [
        { title: '#', value: 'index' },
        { title: 'Product Code', key: 'product_code' },
        { title: 'Barcode', key: 'barcode' },
        { title: 'Description', key: 'description' },
        { title: 'Brand', key: 'brand.brand_name' },
        { title: 'Category', key: 'category.category_name' },
        { title: 'Price', key: 'price' },
        { title: 'Reorder Level', key: 'reorder_level' },
        { title: 'Stock On Hand', key: 'stock_on_hand' },
        { title: 'Actions', key: 'actions', sortable: false }
      ],

      commandOptions: ["Remove From Inventory", "Add to Inventory"],

      options: "Select an Actions",
    };
  },

  watch: {
    options: {
      handler(newOption) {
        if (newOption === 'Remove From Inventory' && this.quantity !== '') {
          this.validateQuantity();
        } else {
          this.quantityError = '';
        }
      },
      deep: true,
    },
    quantity: {
      handler(newQuantity) {
        if (this.options === 'Remove From Inventory' && newQuantity !== '') {
          this.validateQuantity();
        } else {
          this.quantityError = '';
        }
      },
      deep: true,
    },
  },

  computed: {
    ...mapState({
      user: state => state.user,
    }),

    stock_in_by() {
      if (this.user && this.user.first_name && this.user.last_name) {
        return `${this.user.first_name} ${this.user.last_name}`;
      } else {
        return '';
      }
    },

    isSaveButtonDisabled() {
      return this.reference_number === '' || this.options === 'Select an Actions' || this.selectedRow.product_code === '' || this.selectedRow.barcode === '' || this.selectedRow.description === '' || this.remarks === '' || this.quantity === '' || this.user === '' || this.quantityError !== '';
    },

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
    this.validateQuantity();
    // this.$nextTick(() => {
    //   this.$refs.searchField.$refs.searchField.focus();
    // });
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
      axios
        .get('/products', {
          params: {
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
            search: this.searchQuery,
          }
        })
        .then((res) => {
          this.products = res.data.products;
          this.totalItems = res.data.totalItems;
          this.loading = false;
          this.validateQuantity();
        })
        .catch((error) => {
          console.error('Error fetching products:', error);
        });
    },

    filterNumeric(event) {
      const keyCode = event.keyCode || event.which;
      const key = String.fromCharCode(keyCode);

      if (/^\d*\.?\d*$/.test(key)) {
        return;
      }

      event.preventDefault();
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

    async saveStockAdjustment() {
      this.showConfirmationDialog = false;
      const stockAdjustmentData = {
        reference_number: this.reference_number,
        action: this.options,
        product_id: this.selected_product_id,
        quantity: this.quantity,
        user: this.stock_in_by,
        remarks: this.remarks,
      };
      axios
        .post("/stock-adjustment", stockAdjustmentData)
        .then(() => {
          this.getProducts(),
            this.clearForm();
          this.snackbarColor = 'success';
          this.showSnackbar('Stock adjustment saved successfully', 'success');
        })
        .catch((error) => {
          console.error('Error creating stock adjustment', error);
          this.snackbarColor = 'error';
          this.showSnackbar('Failed to save stock adjustment. Please try again later.', 'error');
        });
    },

    validateQuantity() {
      if (this.options === 'Remove From Inventory' && this.quantity !== null) {
        if (this.quantity > this.selectedRow.stock_on_hand) {
          this.quantityError = 'Cannot remove more products than available in stock.';
        } else {
          this.quantityError = '';
        }
      }
    },

    renderProductCategory(category) {
      return category.category ? category.category.category_name : 'Unknown';
    },

    renderProductBrand(brand) {
      return brand.brand ? brand.brand.brand_name : 'Unknown';
    },

    clearForm() {
      this.reference_number = '';
      this.Options = 'Select an Actions';
      this.selectedRow = {
        product_code: '',
        barcode: '',
        description: '',
      };
      this.quantity = '';
      this.remarks = '';
    },

    showConfirmation() {
      this.showConfirmationDialog = true;
    },

    cancelSave() {
      this.showConfirmationDialog = false;
    },

    showSnackbar(text, color, timeout = 3000) {
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

    fetchProduct(product) {
      axios
        .get('/stock-in/generate-reference-number')
        .then(response => {
          this.reference_number = response.data.reference_number;
          this.selectedRow.product_code = product.product_code;
          this.selectedRow.barcode = product.barcode;
          this.selectedRow.description = product.description;
          this.selected_product_id = product.id;
          this.selectedRow.stock_on_hand = product.stock_on_hand;
          this.snackbarColor = 'success';
          this.showSnackbar('Product information fetched successfully', 'success');
        })
        .catch(error => {
          console.error('Error fetching reference number', error);

          // Show error snackbar
          this.showSnackbar('Failed to fetch product information. Please try again later.', 'error');
        });
    },
  },
};
</script>

<style scoped>
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
  