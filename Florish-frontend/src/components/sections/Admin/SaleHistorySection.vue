<template>
  <v-container class="mt-12">
    <v-row>
      <v-col cols="12" sm="4" md="4" class="mt-5">
        <SearchField @search="handleSearch" :searchType="'regular'" />
      </v-col>
      <v-col cols="12" sm="3" md="5" class="mt-3">
        <v-row>
          <v-col cols="12">
            <FilterByDate @date-range-change="handleDateRangeChange" @filter-type-change="handleFilterTypeChange" />
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" sm="4" md="3" class="mt-4">
        <v-card class="pa-3 total-card">
          <span class="total-label">Total of All Total: </span>
          <span class="loading-message" v-if="loadingTotal">Loading...</span>
          <span class="total-value" v-else-if="totalOfAllTotalValue !== null">{{ totalOfAllTotalValue }}</span>
          <span class="loading-message" v-else>Loading...</span>
        </v-card>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12">
        <v-data-table :headers="headers" :items="transactions" :loading="loading" :page="currentPage"
          :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
          @update:options="debouncedGetTransactions" fixed-header height="400">
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
              <td>{{ item.transaction_total }}</td>
              <td>{{ item.payment }}</td>
              <td>{{ item.change }}</td>
              <td>{{ item.transaction_date }}</td>
              <td>{{ item.user.first_name }}</td>
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

                <v-btn :disabled="currentPage === totalPages" class="pagination-button" @click="nextPage" color="#23b78d">
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
</template>
  
<script>
import FilterByDate from "../../commons/FilterByDate.vue";
import SearchField from '../../commons/SearchField.vue';
import _debounce from 'lodash/debounce';
import axios from "axios";

export default {
  name: "SalesHistory",
  components: {
    SearchField,
    FilterByDate,
  },

  data() {
    return {
      itemsPerPage: 10,
      currentPage: 1,
      id: 1,
      totalItems: 0,
      loading: true,
      loadingTotal: false,
      showForm: false,
      totalOfAllTotalValue: null,
      transactions: [],
      fromDate: '',
      toDate: '',
      filterType: '',
      searchQuery: '',
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
        { title: "Over All Total", key: 'transaction_total' },
        { title: "Payment", key: 'payment' },
        { title: "Change", key: 'change' },
        { title: "Transaction Date", key: 'transaction_date' },
        { title: "Transacted By", key: 'user.first_name' },
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
    await this.debouncedGetTransactions();
    await this.fetchTotalOfAllTotal();
    // this.$nextTick(() => {
    //   this.$refs.searchField.$refs.searchField.focus();
    // });
  },

  methods: {
    debouncedGetTransactions: _debounce(function () {
      this.getTransactions();
    }, 1000),

    handleSearch(query) {
      this.searchQuery = query;
      this.currentPage = 1;
      this.debouncedGetTransactions();
    },

    async getTransactions() {
      this.loading = true;
      this.loadingTotal = true;
      try {
        let params = {
          page: this.currentPage,
          itemsPerPage: this.itemsPerPage,
          fromDate: this.fromDate,
          toDate: this.toDate,
          search: this.searchQuery,
        };

        if (this.filterType) {
          switch (this.filterType) {
            case 'Day':
              params.filterType = 'Day';
              params.selectedDate = this.selectedDate;
              break;
            case 'Week':
              params.filterType = 'Week';
              params.selectedDate = this.selectedDate;
              break;
            case 'Month':
              params.filterType = 'Month';
              params.selectedDate = this.selectedDate;
              break;
            case 'Year':
              params.filterType = 'Year';
              params.selectedDate = this.selectedDate;
              break;
            case 'Customize':
              params.filterType = 'Customize';
              break;
          }
        }

        const response = await axios.get('/transactions', { params });

        this.transactions = response.data.transactions;
        this.totalItems = response.data.totalItems;
        this.loading = false;

        await this.fetchTotalOfAllTotal();
      } catch (error) {
        console.error('Error fetching transactions:', error);
        this.loading = false;
      } finally {
        this.loadingTotal = false;
      }
    },

    async fetchTotalOfAllTotal() {
      try {
        this.loadingTotal = true;
        let params = {
          fromDate: this.fromDate,
          toDate: this.toDate,
          search: this.searchQuery,
        };

        switch (this.filterType) {
          case 'Day':
            params.filterType = 'Day';
            params.selectedDate = this.selectedDate;
            break;
          case 'Week':
            params.filterType = 'Week';
            params.selectedDate = this.selectedDate;
            break;
          case 'Month':
            params.filterType = 'Month';
            params.selectedDate = this.selectedDate;
            break;
          case 'Year':
            params.filterType = 'Year';
            params.selectedDate = this.selectedDate;
            break;
          case 'Customize':
            params.filterType = 'Customize';
            break;
        }

        const response = await axios.get('/all-transactions-total', { params });
        this.totalOfAllTotalValue = response.data.total;
      } catch (error) {
        console.error('Error fetching total of all total', error);
        this.totalOfAllTotalValue = 0;
      } finally {
        this.loadingTotal = false;
      }
    },

    handleFilterTypeChange(newFilterType) {
      this.filterType = newFilterType;
      this.currentPage = 1;

      if (['Day', 'Week', 'Month', 'Year'].includes(newFilterType)) {
        this.filterByDay = null;
        this.fromDate = null;
        this.toDate = null;
      }

      this.debouncedGetTransactions();
    },

    handleDateRangeChange({ fromDate, toDate, selectedDate }) {
      if (['Day', 'Week', 'Month', 'Year'].includes(this.filterType)) {
        this.filterByDay = null;
        this.fromDate = null;
        this.toDate = null;
        this.selectedDate = selectedDate;
      } else {
        this.fromDate = fromDate;
        this.toDate = toDate;
        this.selectedDate = null;
      }

      this.currentPage = 1;
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
  /* margin-top: 20px; */
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
  background-color: #23b78d;
  color: #fff;
  border-color: #23b78d;
}
</style>
