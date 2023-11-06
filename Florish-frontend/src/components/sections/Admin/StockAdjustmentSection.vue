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
          <v-data-table :headers="headers" :items="products" :loading="loading" :page="currentPage"
            :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1"
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
                <td>{{ item.brand.brand_name }}</td>
                <td>{{ item.category.category_name }}</td>
                <td>{{ item.price }}</td>
                <td>{{ item.reorder_level }}</td>
                <td>{{ item.stock_on_hand }}</td>
                <td>
                  <span>
                    <v-icon @click="fetchProduct(item)">mdi-arrow-right</v-icon>
                  </span>
                </td>
              </tr>
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
          <v-text-field v-model="quantity" label="Quantity" placeholder="Enter Quantity" @input="validateQuantity" />
          <div v-if="quantityError" class="text-error">{{ quantityError }}</div>
        </v-col>
        <v-col>
          <v-text-field v-model="user" label="User" placeholder="USER" :model-value="stock_in_by" readonly />
        </v-col>
      </v-row>
      <v-row class="mt-3">
        <v-col cols="2" offset-md="10">
          <v-btn @click="showConfirmation" :disabled="isSaveButtonDisabled" color="success"
            style="width: 150px">Save</v-btn>
        </v-col>
      </v-row>
      <v-dialog v-model="showConfirmationDialog" max-width="400" class="center-dialog  no-background">
        <v-card>
          <v-card-title>
            <v-icon left>mdi-alert-circle-outline</v-icon>
            Confirm Save
          </v-card-title>
          <v-card-text class="text-center">
            <v-icon left>mdi-comment-question</v-icon>
            ARE YOU SURE YOU WANT TO SAVE THIS RECORDS?
          </v-card-text>
          <v-card-actions class="d-flex justify-center">
            <div>
              <v-btn color="success" @click="saveStockAdjustment" style="width: 150px;">Save</v-btn>
              <v-btn @click="cancelSave">Cancel</v-btn>
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
  },

  async mounted() {
    await this.getProducts();
    this.validateQuantity();
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
  