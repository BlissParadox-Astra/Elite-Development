<template>
  <v-container class="section2 mt-14">
    <v-row cols="12">
      <v-col cols="12" sm="9">
        <FilterByDate @date-range-change="handleDateRangeChange" @filter-type-change="handleFilterTypeChange" />
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12">
        <v-data-table :headers="headers" :items="stockIns" :loading="loading" :page="currentPage"
          :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
          @update:options="debouncedGetStockIns" fixed-header height="450">
          <template v-slot:item="{ item, index }">
            <tr>
              <td>{{ displayedIndex + index }}</td>
              <td>{{ item.reference_number }}</td>
              <td>{{ item.adjusted_product.product_code }}</td>
              <td>{{ item.adjusted_product.barcode }}</td>
              <td>{{ item.adjusted_product.description }}</td>
              <td>{{ item.quantity_added }}</td>
              <td>{{ item.stock_in_date }}</td>
              <td>{{ item.stock_in_by_user.first_name }}</td>
            </tr>
          </template>
          <template v-slot:bottom>
            <v-col cols="12">
              <div v-if="totalPages > 1" class="text-center pt-5 pagination">
                <v-btn class="pagination-button" @click="previousPage" color="#23b78d" :disabled="currentPage === 1">
                  <v-icon>mdi-chevron-left</v-icon> Prev
                </v-btn>

                <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber" @click="gotoPage(pageNumber)"
                  :class="{ active: pageNumber === currentPage }" class="pagination-button">
                  {{ pageNumber }}
                </v-btn>

                <v-btn class="pagination-button" @click="nextPage" color="#23b78d" :disabled="currentPage === totalPages">
                  Next <v-icon>mdi-chevron-right</v-icon>
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
import FilterByDate from '../../commons/FilterByDate.vue';
import _debounce from 'lodash/debounce';
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
      currentPage: 1,
      id: 1,
      stockIns: [],
      fromDate: '',
      toDate: '',
      filterType: '',
      headers: [
        { title: '#', value: 'index' },
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

  computed: {
    displayedIndex() {
      return (this.currentPage - 1) * this.itemsPerPage + 1;
    },

    totalPages() {
      return Math.ceil(this.totalItems / this.itemsPerPage);
    },

    visiblePageRange() {
      const smallScreenMaxPages = 2;
      const largeScreenMaxPages = 5;

      const maxVisiblePages = this.isSmallScreen ? smallScreenMaxPages : largeScreenMaxPages;
      const halfMaxVisiblePages = Math.floor(maxVisiblePages / 2);
      const firstPage = Math.max(1, this.currentPage - halfMaxVisiblePages);
      const lastPage = Math.min(this.totalPages, firstPage + maxVisiblePages - 1);

      const range = [];
      for (let i = firstPage; i <= lastPage; i++) {
        range.push(i);
      }

      return range;
    },

    isSmallScreen() {
      return window.innerWidth < 768;
    },
  },

  async mounted() {
    await this.debouncedGetStockIns();
  },

  methods: {
    debouncedGetStockIns: _debounce(function () {
      this.getStockIns();
    }, 1000),

    async getStockIns() {
      this.loading = true;
      try {
        let params = {
          page: this.currentPage,
          itemsPerPage: this.itemsPerPage,
          fromDate: this.fromDate,
          toDate: this.toDate,
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

        const response = await axios.get('/stock-ins', { params });

        this.stockIns = response.data.stockIns;
        this.totalItems = response.data.totalItems;
        this.loading = false;
      } catch (error) {
        console.error('Error fetching stock in records:', error);
        this.loading = false;
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

      this.debouncedGetStockIns();
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
      this.debouncedGetStockIns();
    },

    previousPage() {
      this.loading = true;
      if (this.currentPage > 1) {
        this.currentPage--;
        this.debouncedGetStockIns();
      }
    },

    nextPage() {
      this.loading = true;
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.debouncedGetStockIns();
      }
    },

    gotoPage(pageNumber) {
      this.loading = true;
      this.currentPage = pageNumber;
      this.debouncedGetStockIns();
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

<style scoped>
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
  