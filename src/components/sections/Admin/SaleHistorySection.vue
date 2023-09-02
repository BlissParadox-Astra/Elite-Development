<template>
  <v-main>
    <v-container class="mt-6">
      <v-row justify="center">
        <v-col cols="12">
          <h2 class="text-center">SALES HISTORY</h2>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" sm="9">
          <FilterByDate />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="success" block>
            SORT BY
            <v-menu activator="parent">
              <v-list>
                <v-list-item v-for="(item, index) in items" :key="index" :value="index">
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-btn>
        </v-col>
      </v-row>
      <v-row justify="center">
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="products" height="460px" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-card class="pa-3 total-card">
            <v-row class="text-right">
              <v-col v-for="(column, index) in tableColumns" :key="index" cols="3">
                <span class="total-value">{{ calculateTotal(column.total) }}</span>
              </v-col>
            </v-row>
          </v-card>
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
        { key: "invoiceNo", label: "Invoice No." },
        { key: "productCode", label: "Product Code" },
        { key: "barcode", label: "Bar Code" },
        { key: "description", label: "Description" },
        { key: "price", label: "Price" },
        { key: "quantity", label: "Quantity" },
        { key: "total", label: "Total" },
        { key: "transacBy", label: "Transaction By" },
      ],

      products: [
        { invoiceNo: "Invoice001", productCode: "P001", barcode: "123456789", description: "Product 1", price: 30, quantity: "5", total: "800", transacBy: "cashier", },
        { invoiceNo: "Invoice002", productCode: "P002", barcode: "987654321", description: "Product 2", price: 400, quantity: "6", total: "1000",  transacBy: "cashier", },
      ],

      items: [
        { title: 'Category' },
        { title: 'Total' },
        { title: 'Alphabetically' },
      ],
    };
  },
  
  methods: {
    calculateTotal(total) {
      return total;
    },
  },
};
</script>

<style scoped>
.total-card {
  width: 20%;
  float: right;
  background-color: #d8d3d3;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-top: 20px;
}
.total-value {
  font-weight: bold;
  color: #333;
}
</style>
  