<template>
  <v-container class="mt-5 section2">
    <v-row justify="center">
      <v-col cols="12">
        <h2 class="text-center">STOCK IN HISTORY</h2>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" sm="9">
        <FilterByDate />
      </v-col>
      <v-col cols="12" sm="3" class="d-flex justify-center align-center">
        <v-btn color="success" block @click="showProductForm">Load Record</v-btn>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12">
        <CustomTable :columns="tableColumns" :items="products" height="450px" />
      </v-col>
    </v-row>
  </v-container>
</template>
  
<script>
import CustomTable from '../../common/CustomTable.vue';
import FilterByDate from '../../common/FilterByDate.vue';

export default {
  name: "StockHistory",
  components: {
    CustomTable,
    FilterByDate,
  },
  data() {
    return {
      tableColumns: [
        { key: "referenceNo", label: "Reference No." },
        { key: "productCode", label: "Product Code" },
        { key: "barcode", label: "Barcode" },
        { key: "description", label: "Description" },
        { key: "quantity", label: "Quantity" },
        { key: "stockInDate", label: "Stock In Date" },
        { key: "stockInBy", label: "Stock In By" },
      ],
      products: [
        {
          referenceNo: "REF001",
          productCode: "P001",
          barcode: "123456789",
          description: "Product 1",
          quantity: 10,
          stockInDate: "2023-08-09",
          stockInBy: "John Doe",
        },
        {
          referenceNo: "REF002",
          productCode: "P002",
          barcode: "987654321",
          description: "Product 2",
          quantity: 20,
          stockInDate: "2023-08-10",
          stockInBy: "Jane Smith",
        },
      ],
    };
  },
  methods: {
    loadRecord() {
      // Implement your load record functionality here
    },
  },
};
</script>
  