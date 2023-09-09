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
        <CustomTable v-if="stockIns.length > 0" :columns="tableColumns" :items="stockIns" height="450px" />
        <div v-else>No stock in records available.</div>
      </v-col>
    </v-row>
  </v-container>
</template>
  
<script>
import CustomTable from '../../common/CustomTable.vue';
import FilterByDate from '../../common/FilterByDate.vue';
import axios from 'axios';

export default {
  name: "StockHistory",
  components: {
    CustomTable,
    FilterByDate,
  },
  data() {
    return {
      stockIns: [],
      tableColumns: [
        { key: "reference_number", label: "Reference No." },
        { key: "product_code", label: "Product Code", render: this.renderProductCode },
        { key: "barcode", label: "Barcode", render: this.renderProductBarcode },
        { key: "description", label: "Description", render: this.renderProductDescription },
        { key: "quantity_added", label: "Quantity Added" },
        { key: "stock_in_date", label: "Stock In Date" },
        { key: "stock_in_by", label: "Stock In By", render: this.renderStockInBy },
      ],
    };
  },

  mounted() {
    this.getStockIns();
  },

  methods: {
    async getStockIns() {
      try {
        const response = await axios.get('/stockIns');
        this.stockIns = response.data.stock_ins; // Make sure to access the correct property
        console.log('Records:', this.stockIns);
      } catch (error) {
        console.error('Error fetching stock in records:', error);
      }
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
      if (stock_in_by_user.stock_in_by_user) {
        const { first_name, last_name } = stock_in_by_user.stock_in_by_user;
        return `${first_name} ${last_name}`;
      } else {
        return 'Unknown';
      }
    },

  },
};
</script>
  