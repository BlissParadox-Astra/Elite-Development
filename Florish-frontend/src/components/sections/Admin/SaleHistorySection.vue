<template>
  <v-main>
    <v-container class="mt-6">
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
          <v-data-table :headers="headers" :items="transactions" :loading="loading" :page="currentPage"
            :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1"
            @update:options="getTransactions" fixed-header height="400">
            <template v-slot:custom-sort="{ header }">
              <span v-if="header.key === 'actions'">Actions</span>
            </template>
            <template v-slot:item="{ item, index }">
              <tr>
                <td>{{ displayedIndex + index }}</td>
                <td>{{ item.invoice_number }}</td>
                <td>{{ item.product_code ? item.transacted_product.product_code: 'Unknown product code' }}</td>
                <td>{{ item.barcode ? item.transacted_product.barcode: 'Unknown barcode' }}</td>
                <td>{{ item.description ? item.transacted_product.description: 'Unknown description' }}</td>
                <td>{{ item.category_name ? item.transacted_product.category.category_name: 'Unknown description' }}</td>
                <td>{{ item.price }}</td>
                <td>{{ item.quantity }}</td>
                <td>{{ item.total }}</td>
                <td>{{ item.transaction_date }}</td>
                <td>{{ item.user.first_name }}</td>
              </tr>
            </template>
          </v-data-table>
          <!-- <CustomTable :columns="tableColumns" :items="products" height="460px" /> -->
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" xl="5" lg="3">
          <v-card class="pa-3 total-card">
            <span class="total-label">Total of All Total: </span>
            <span class="total-value">{{ totalOfAllTotal }}</span>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
  
<script>
import axios from "axios";
import FilterByDate from "../../commons/FilterByDate.vue";

export default {
  name: "SalesHistory",
  components: {
    FilterByDate,
  },

  data() {
    return {
      itemsPerPage: 10,
      currentPage: 1,
      id: 1,
      totalItems: 0,
      loading: true,
      showForm: false,
      transactions: [],
      sortByOptions: ["Category", "Total", "Alphabetically"],
      headers: [
        { title: '#', value: 'index' },
        { title: "Invoice No.", key: 'invoice_number' },
        { title: "Product Code", key: 'transacted_product.product_code' },
        { title: "Barcode", key: 'transacted_product.barcode' },
        { title: "Description", key: 'transacted_product.description' },
        {title: "Category", key: 'transacted_product.category.category_name'},
        { title: "Price", key: 'price' },
        { title: "Quantity", key: 'quantity' },
        { title: "Total", key: 'total' },
        { title: "Transacted Date", key: 'transaction_date' },
        { title: "Transacted By", key: 'user.first_name' },
      ],
      items: [
        { title: 'Category' },
        { title: 'Total' },
        { title: 'Alphabetically' },
      ],
    };
  },

  computed: {
    displayedIndex() {
      return (this.currentPage - 1) * this.itemsPerPage + 1;
    },

    totalOfAllTotal() {
      return this.transactions.reduce((total, item) => total + parseFloat(item.total), 0);
    },
  },

  async mounted() {
    await this.getTransactions();
  },

  methods: {
    getTransactions() {
      this.loading = true;
      axios
        .get('/transactions', {
          params: {
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
          }
        })
        .then((res) => {
          this.transactions = res.data.transactions;
          this.totalItems = res.data.totalItems;
          this.loading = false;
        });
    },
    renderProductCode(transactions) {
      return transactions.transacted_product ? transactions.transacted_product.product_code : 'Unknown';
    },

    renderBarCode(transactions) {
      return transactions.transacted_product ? transactions.transacted_product.barcode : 'Unknown';
    },

    renderDescription(transactions) {
      return transactions.transacted_product ? transactions.transacted_product.description : 'Unknown';
    },

    renderProductCategory(transactions) {
      if (transactions.transacted_product && transactions.transacted_product.category) {
        return transactions.transacted_product.category.category_name;
      } else {
        return 'Unknown';
      }
    },

    renderUser(user) {
      if (user.user) {
        const { first_name, last_name } = user.user;
        return `${first_name} ${last_name}`;
      } else {
        return 'Unknown';
      }
    },
  },
};
</script>

<style scoped>
.total-card {

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
  