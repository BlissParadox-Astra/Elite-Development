<template>
    <v-container class="mt-14">
        <v-row>
            <v-col cols="12" sm="9">
                <FilterByDate @date-range-change="handleDateRangeChange" @filter-type-change="handleFilterTypeChange" />
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="12">
                <v-data-table :headers="headers" :items="adjustments" :loading="loading" :page="currentPage"
                    :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
                    @update:options="debouncedStockAdjustments" fixed-header height="400">
                    <template v-slot:custom-sort="{ header }">
                        <span v-if="header.key === 'actions'">Actions</span>
                    </template>
                    <template v-slot:item="{ item, index }">
                        <tr>
                            <td>{{ displayedIndex + index }}</td>
                            <td>{{ item.reference_number }}</td>
                            <td>{{ item.action }}</td>
                            <td>{{ item.adjusted_product.product_code }}</td>
                            <td>{{ item.adjusted_product.barcode }}</td>
                            <td>{{ item.adjusted_product.description }}</td>
                            <td>{{ item.remarks }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.adjustment_date }}</td>
                            <td>{{ item.stock_adjustment_by_user.first_name }}</td>
                        </tr>
                    </template>
                    <template v-slot:bottom>
                        <v-col cols="12">
                            <div v-if="totalPages > 1" class="text-center pt-5 pagination">
                                <v-btn class="pagination-button" @click="previousPage" color="#23b78d"
                                    :disabled="currentPage === 1">
                                    <v-icon>mdi-chevron-left</v-icon> Prev
                                </v-btn>

                                <v-btn v-for="pageNumber in visiblePageRange" :key="pageNumber"
                                    @click="gotoPage(pageNumber)" :class="{ active: pageNumber === currentPage }"
                                    class="pagination-button">
                                    {{ pageNumber }}
                                </v-btn>

                                <v-btn class="pagination-button" @click="nextPage" color="#23b78d"
                                    :disabled="currentPage === totalPages">
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
    name: "StockAdjustmentHistory",
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
            adjustments: [],
            fromDate: '',
            toDate: '',
            filterType: '',
            headers: [
                { title: '#', value: 'index' },
                { title: "Reference No.", key: "reference_number" },
                { title: "Action", key: "action" },
                { title: "Product Code", key: "adjusted_product.product_code" },
                { title: "Barcode", key: "adjusted_product.barcode" },
                { title: "Description", key: "adjusted_product.description" },
                { title: "Remarks", key: "remarks" },
                { title: 'Quantity', key: 'quantity' },
                { title: 'Adjustment Date', key: 'adjustment_date' },
                { title: 'User', key: 'stock_adjustment_by_user.first_name' },
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
        await this.debouncedStockAdjustments();
    },

    methods: {
        debouncedStockAdjustments: _debounce(function () {
            this.getStockAdjustments();
        }, 1000),

        async getStockAdjustments() {
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

                const response = await axios.get('/stock-adjustments', { params });

                this.adjustments = response.data.stockAdjustments;
                this.totalItems = response.data.totalItems;
                this.loading = false;
            } catch (error) {
                console.error('Error fetching stock adjustment records:', error);
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

            this.debouncedStockAdjustments();
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
            this.debouncedStockAdjustments();
        },

        previousPage() {
            this.loading = true;
            if (this.currentPage > 1) {
                this.currentPage--;
                this.debouncedStockAdjustments();
            }
        },

        nextPage() {
            this.loading = true;
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.debouncedStockAdjustments();
            }
        },

        gotoPage(pageNumber) {
            this.loading = true;
            this.currentPage = pageNumber;
            this.debouncedStockAdjustments();
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

        renderUser(stock_adjustment_by_user) {
            if (stock_adjustment_by_user.stock_adjustment_by_user) {
                const { first_name, last_name } = stock_adjustment_by_user.stock_adjustment_by_user;
                return `${first_name} ${last_name}`;
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
