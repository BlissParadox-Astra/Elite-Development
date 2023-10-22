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
          </v-data-table-server>
          <!-- <CustomTable v-if="products.length > 0" :columns="tableColumns" :items="products" :showFetchIcon="true" @fetch-data="fetchProduct"
            height="480px" /> -->
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
import SearchField from "../../commons/SearchField.vue";
import axios from 'axios'

export default {
  name: "StockAdjustment",
  components: {
    SearchField,
  },
  data() {
    return {
      itemsPerPage: 10,
      page: 1,
      id: 1,
      totalItems: 0,
      loading: true,
      products: [],
      selectedRow: {
        product_code: "",
        barcode: "",
        description: "",
      },
      headers: [
        { title: '#', key: 'id' },
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

      Options: "Select an Actions",
    };
  },

  async mounted() {
    await this.getProducts();
  },

  methods: {
    getProducts() {
      axios
        .get('/products', {
          params: {
            page: this.page,
            itemsPerPage: this.itemsPerPage,
          }
        })
        .then((res) => {
          this.products = [...res.data.products.data];
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching products:', error);
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
  