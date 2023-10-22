<template>
  <v-container class="mt-5 section2">
    <v-row>
      <v-col cols="12" sm="9">
        <FilterByDate />
      </v-col>
      <v-col cols="12" sm="3" class="d-flex justify-center align-center">
        <v-btn color="success" block>Load Record</v-btn>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12">
        <v-data-table-server v-model:items-per-page="itemsPerPage" :page="page" :headers="headers"
          :items-length="totalItems" :items="stockIns" :loading="loading" item-value="id" class="elevation-1"
          @update:options="getStockIns">
          <template v-slot:item="{ item }">
            <tr>
              <td>{{ item.id }}</td>
              <td>{{ item.reference_number }}</td>
              <td>{{ item.adjusted_product.product_code }}</td>
              <td>{{ item.adjusted_product.barcode }}</td>
              <td>{{ item.adjusted_product.description }}</td>
              <td>{{ item.quantity_added }}</td>
              <td>{{ item.stock_in_date }}</td>
              <td>{{ item.stock_in_by_user.first_name}}</td>
            </tr>
          </template>
        </v-data-table-server>
      </v-col>
    </v-row>
  </v-container>
</template>
  
<script>
import FilterByDate from '../../commons/FilterByDate.vue';
import axios from 'axios';

export default {
  name: "StockHistory",
  components: {
    FilterByDate,
  },
  data() {
    return {
      itemsPerPage: 10,
      totalItems: 0,
      loading: true,
      page: 1,
      id: 1,
      stockIns: [],
      headers: [
        { title: '#', key: 'id' },
        { title: 'Reference No.', key: 'reference_number' },
        { title: 'Product Code', key: 'adjusted_product.product_code' },
        { title: 'Barcode', key: 'adjusted_product.barcode' },
        { title: 'Description', key: 'adjusted_product.description' },
        { title: 'Quantity Added', key: 'quantity_added' },
        { title: 'Stock In Date', key: 'stock_in_date' },
        { title: 'Stock In By', key: 'stock_in_by_user.first_name' },
      ],
    };
  },

  async mounted() {
    await this.getStockIns();
  },

  methods: {
    getStockIns() {
      this.loading = true;
      axios
        .get('/stock-ins', {
          params: {
            page: this.page,
            itemsPerPage: this.itemsPerPage,
          }
        })
        .then((res) => {
          this.stockIns = res.data.stockIns;
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching stock in records:', error);
        });
    },

    renderProductCode(adjusted_product) {
      return adjusted_product.adjusted_product ? adjusted_product.adjusted_product.product_code : 'Unknown';
    },

    renderProductBarcode(adjusted_product) {
      return adjusted_product.adjusted_product ? adjusted_product.adjusted_product.barcode : 'Unknown';
    },

    renderProductDescription(adjusted_product) {
      return adjusted_product.adjusted_product ? adjusted_product.adjusted_product.description : 'Unknown';
    },

    renderStockInBy(stock_in_by_user) {
      if (stock_in_by_user.first_name) {
        return `${stock_in_by_user.first_name} ${stock_in_by_user.last_name}`;
      } else {
        return 'Unknown';
      }
    },

  },
};
</script>
  