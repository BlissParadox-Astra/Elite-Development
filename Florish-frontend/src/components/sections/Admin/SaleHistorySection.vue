<template>
  <v-container class="mt-12">
    <v-row>
      <v-col cols="12" sm="6" md="4" class="mt-5">
        <SearchField @search="handleSearch" :searchType="'regular'" />
      </v-col>
      <v-col cols="12" sm="6" md="5" class="mt-3">
        <v-row>
          <v-col cols="12">
            <FilterByDate @date-range-change="handleDateRangeChange" @filter-type-change="handleFilterTypeChange" />
          </v-col>
          <v-col cols="6" v-if="filterType === 'Day'">
            <v-text-field v-model="filterByDay" label="Select Date" type="date" outlined
              @change="handleFilterByDay"></v-text-field>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" sm="6" md="3" class="mt-4">
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
              <td>{{ item.transaction_date }}</td>
              <td>{{ item.user.first_name }}</td>
            </tr>
          </template>
          <template v-slot:bottom>
            <div class="text-center pt-5 pagination">
              <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                :disabled="currentPage === 1"><v-icon>mdi-chevron-left</v-icon> Prev</v-btn>

              <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                :class="{ active: pageNumber === currentPage }" class="pagination-button">
                {{ pageNumber }}
              </v-btn>

              <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                :disabled="currentPage === totalPages">Next <v-icon>mdi-chevron-right</v-icon></v-btn>
            </div>
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
    const asiaManilaTimezone = 'Asia/Manila';
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Set time to midnight to remove time information
    const currentDateInManila = currentDate.toLocaleDateString('en-CA', { timeZone: asiaManilaTimezone });
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
      filterByDay: currentDateInManila,
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
    await this.handleFilterByDay();
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
              params.selectedDate = this.filterByDay;
              break;
            case 'Week':
              params.filterType = 'Week';
              break;
            case 'Month':
              params.filterType = 'Month';
              break;
            case 'Year':
              params.filterType = 'Year';
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
            params.selectedDate = this.filterByDay;
            break;
          case 'Week':
            params.filterType = 'Week';
            break;
          case 'Month':
            params.filterType = 'Month';
            break;
          case 'Year':
            params.filterType = 'Year';
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

    handleFilterByDay() {
      this.currentPage = 1;
      this.debouncedGetTransactions();
    },

    handleFilterTypeChange(newFilterType) {
      this.filterType = newFilterType;
      this.currentPage = 1;

      if (this.filterType !== 'Day') {
        this.filterByDay = null;
      }

      this.debouncedGetTransactions();
    },

    handleDateRangeChange({ fromDate, toDate }) {
      this.fromDate = fromDate;
      this.currentPage = 1;
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
