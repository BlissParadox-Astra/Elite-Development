<template>
  <v-main>
    <v-container class="mt-6">
      <v-row>
        <v-col cols="12" sm="9">
          <FilterByDate @date-range-change="handleDateRangeChange" />
        </v-col>
        <v-col cols="12" sm="3" class="d-flex justify-center align-center">
          <v-btn color="#23b78d" block>
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
            :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
            @update:options="debouncedGetTransactions" fixed-header>
            <template v-slot:custom-sort="{ header }">
              <span v-if="header.key === 'actions'">Actions</span>
            </template>
            <template v-slot:item="{ item, index }">
              <tr>
                <td>{{ displayedIndex + index }}</td>
                <td>{{ item.transaction_number }}</td>
                <td>{{ item.transacted_product.product_code }}</td>
                <td>{{ item.transacted_product.barcode }}</td>
                <td>{{ item.transacted_product.description }}</td>
                <td>{{ item.transacted_product.category.category_name }}</td>
                <td>{{ item.price }}</td>
                <td>{{ item.quantity }}</td>
                <td>{{ item.total }}</td>
                <td>{{ item.transaction_date }}</td>
                <td>{{ item.user.first_name }}</td>
              </tr>
            </template>
            <template v-slot:bottom>
              <div class="text-center pt-8 pagination">
                <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                  :disabled="currentPage === 1">Previous</v-btn>

                <v-btn v-for="pageNumber in totalPages" :key="pageNumber" @click="gotoPage(pageNumber)"
                  :class="{ active: pageNumber === currentPage }" class="pagination-button">
                  {{ pageNumber }}
                </v-btn>

                <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                  :disabled="currentPage === totalPages">Next</v-btn>
              </div>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" xl="5" lg="3">
          <v-card class="pa-3 total-card">
            <span class="total-label">Total of All Total: </span>
            <span class="total-value" v-if="totalOfAllTotalValue !== null">{{ totalOfAllTotalValue }}</span>
            <span class="loading-message" v-else>Loading...</span>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
  
<script>
import FilterByDate from "../../commons/FilterByDate.vue";
import _debounce from 'lodash/debounce';
import axios from "axios";

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
      totalOfAllTotalValue: null,
      transactions: [],
      fromDate: '',
      toDate: '',
      sortByOptions: ["Category", "Total", "Alphabetically"],
      headers: [
        { title: '#', value: 'index' },
        { title: "Invoice No.", key: 'transaction_number' },
        { title: "Product Code", key: 'transacted_product.product_code' },
        { title: "Barcode", key: 'transacted_product.barcode' },
        { title: "Description", key: 'transacted_product.description' },
        { title: "Category", key: 'transacted_product.category.category_name' },
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

    totalPages() {
      return Math.ceil(this.totalItems / this.itemsPerPage);
    },
  },

  async mounted() {
    await this.debouncedGetTransactions();
    await this.fetchTotalOfAllTotal();
  },

  methods: {
    debouncedGetTransactions: _debounce(function () {
      this.getTransactions();
    }, 1000),

    async getTransactions() {
      this.loading = true;
      try {
        const response = await axios.get('/transactions', {
          params: {
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
            fromDate: this.fromDate,
            toDate: this.toDate,
          }
        });
        this.transactions = response.data.transactions;
        this.totalItems = response.data.totalItems;
        this.loading = false;

        await this.fetchTotalOfAllTotal();
      } catch (error) {
        console.error('Error fetching transactions:', error);
        this.loading = false;
      }
    },

    async fetchTotalOfAllTotal() {
      try {
        const response = await axios.get('/all-transactions-total', {
          params: {
            fromDate: this.fromDate,
            toDate: this.toDate,
          }
        });
        this.totalOfAllTotalValue = response.data.total;
      } catch (error) {
        console.error('Error fetching total of all total', error);
        this.totalOfAllTotalValue = 0;
      }
    },

    handleDateRangeChange({ fromDate, toDate }) {
      this.fromDate = fromDate;
      this.toDate = toDate;
      this.debouncedGetTransactions();
    },

    previousPage() {
      this.loading = true;
      if (this.currentPage > 1) {
        this.currentPage--;
        this.debouncedGetTransactions();
      }
    },

    nextPage() {
      this.loading = true;
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.debouncedGetTransactions();
      }
    },

    gotoPage(pageNumber) {
      this.loading = true;
      this.currentPage = pageNumber;
      this.debouncedGetTransactions();
    },

    renderProductCode(transacted_product) {
      return transacted_product.transacted_product ? transacted_product.transacted_product.product_code : 'Unknown';
    },

    renderBarCode(transacted_product) {
      return transacted_product.transacted_product ? transacted_product.transacted_product.barcode : 'Unknown';
    },

    renderDescription(transacted_product) {
      return transacted_product.transacted_product ? transacted_product.transacted_product.description : 'Unknown';
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
  background-color: #007bff;
  color: #fff;
  border-color: #007bff;
}
</style>
  