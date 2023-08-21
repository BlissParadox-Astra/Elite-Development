<template>
  <v-main>
    <v-container class="mt-5">
      <v-row justify="center">
        <v-col cols="12">
          <h2 class="text-center">TRANSACTION</h2>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" sm="9">
          <FilterByDate />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block> Load Record
          </v-btn>
        </v-col>
      </v-row>
      <v-row justify="center">
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="products" height="450px"/>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
    
<script>
import CustomTable from "../../common/CustomTable.vue";
import FilterByDate from "../../common/FilterByDate.vue";

export default {
  name: "SalesHistory",
  components: {
    CustomTable,
    FilterByDate,
  },
  data() {
    return {
      showForm: false,
      sortByOptions: ["Category", "Total", "Alphabetically"],
      tableColumns: [
        { key: "transactionNo", label: "Transaction No" },
        { key: "transactionDate", label: "Transaction Date" },
        { key: "productCode", label: "Product Code" },
        { key: "description", label: "Description" },
        { key: "price", label: "Price" },
        { key: "quantity", label: "Quantity" },
        { key: "total", label: "Total" },
        { key: "transactionBy", label: "Transaction By" },
      ],
      products: [
        { transactionNo: "REF001", transactionDate: "P001", productCode: "123456789", description: "Product 1", price: "20", quantity: "22", total: 300, transactionBy: "John" },
        { transactionNo: "REF002", transactionDate: "P002", productCode: "987654321", description: "Product 2", price: "15", quantity: "25", total: 400, transactionBy: "John" },
      ],
    };
  },
};
</script>
    