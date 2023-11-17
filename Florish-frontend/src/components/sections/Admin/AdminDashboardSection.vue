<template>
    <v-container class="section2">
        <v-container>
            <v-row class="d-flex justify-center">
                <v-col cols="12">
                    <h2 class="text-center" v-if="user">Welcome! {{ user.user.first_name }} {{ user.user.last_name }}</h2>
                </v-col>
            </v-row>
            <v-row class="text-center">
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>DAILY SALES</v-card-title>
                        <v-card-text>{{ dailySales.toFixed(2) }} PHP</v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>PRODUCT LINE</v-card-title>
                        <v-card-text>{{ productLineCount }} PRODUCT LINES</v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>STOCK ON HAND</v-card-title>
                        <v-card-text>{{ totalStockOnHand }} STOCKS</v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>CRITICAL ITEMS</v-card-title>
                        <v-card-text>{{ criticalStockCount }} ITEMS</v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <v-row class="d-flex justify-center">
                <v-col cols="12" xs="12" sm="8" md="6">
                    <v-card>
                        <v-card-text class="text-center">
                            <v-row class="justify-center">
                                <v-col cols="8">
                                    <canvas ref="myPieChart" width="700" height="200"></canvas>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" xs="12" sm="8" md="6">
                    <v-card>
                        <v-card-text class="text-center">
                            <canvas ref="myMonthlyEarningsChart" width="700" height="460"></canvas>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-container>
</template>
  
<script>
import Chart from "chart.js/auto";
import axios from "axios";
export default {
    name: "DashboardSection",

    data() {
        return {
            user: null,
            dailySales: 0.0,
            productLineCount: 0,
            totalStockOnHand: 0,
            criticalStockCount: 0,
            transactionYears: [],
            monthlySales: [
                { month: "January", value: 300 },
                { month: "February", value: 450 },
                { month: "March", value: 600 },
                { month: "April", value: 300 },
                { month: "May", value: 450 },
                { month: "June", value: 600 },
                { month: "July", value: 300 },
                { month: "August", value: 450 },
                { month: "September", value: 600 },
                { month: "October", value: 300 },
                { month: "November", value: 450 },
                { month: "December", value: 600 },
            ],
        };
    },

    async mounted() {
        const response = await axios.get("/user")
        this.user = response.data;
        await this.fetchTransactionYears();
        await this.fetchMonthlyEarnings();
        await this.createMonthlyEarningsChart();
        await this.createPieChart();
        await this.fetchDailyTransactions();
        await this.fetchProductLineCount();
        await this.fetchTotalStockOnHand();
        await this.fetchcriticalStockCount();
    },

    methods: {
        createPieChart() {
            const canvas = this.$refs.myPieChart;

            if (canvas) {
                const ctx = canvas.getContext("2d");

                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: this.transactionYears.map((item) => item.year),
                        datasets: [
                            {
                                data: this.transactionYears.map((item) => item.earnings),
                                backgroundColor: this.transactionYears.map(this.getRandomColor),
                            },
                        ],
                    },
                    options: {},
                });
            } else {
                console.error('Canvas element not found for pie chart');
            }
        },


        getRandomColor() {
            const letters = "0123456789ABCDEF";
            let color = "#";
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        },

        async fetchTransactionYears() {
            try {
                const response = await axios.get("/transaction-years-earnings"); // Replace with your API endpoint to fetch transaction years and earnings
                this.transactionYears = response.data; // Assuming the API returns an array of objects with 'year' and 'earnings'
            } catch (error) {
                console.error("Error fetching transaction years data", error);
            }
        },

        async fetchMonthlyEarnings() {
            try {
                const response = await axios.get('/monthly-earnings');
                this.monthlyEarnings = response.data.monthlyEarnings;
            } catch (error) {
                console.error('Error fetching monthly earnings data', error);
            }
        },

        createMonthlyEarningsChart() {
            const canvas = this.$refs.myMonthlyEarningsChart;

            if (canvas) {
                const ctx = canvas.getContext('2d');

                const monthNames = this.monthlyEarnings.map(item => item.month_name);
                const monthlyEarnings = this.monthlyEarnings.map(item => item.earnings);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: monthNames,
                        datasets: [
                            {
                                label: 'Monthly Earnings',
                                data: monthlyEarnings,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                            },
                        },
                    },
                });
            } else {
                console.error('Canvas element not found for bar chart');
            }
        },


        async fetchDailyTransactions() {
            try {
                const response = await axios.get('/daily-transactions');
                const dailyTransactionsData = response.data.dailyTransactions;

                if (dailyTransactionsData.length > 0) {
                    this.dailySales = parseFloat(dailyTransactionsData[0].total_amount);
                } else {
                    this.dailySales = 0.0;
                }
            } catch (error) {
                console.error('Error fetching daily transaction data', error);
            }
        },

        async fetchProductLineCount() {
            try {
                const response = await axios.get('/category/product-line-count');
                this.productLineCount = response.data.productLineCount;
            } catch (error) {
                console.error('Error fetching product line count', error);
            }
        },

        async fetchTotalStockOnHand() {
            try {
                const response = await axios.get('/product/total-stock');
                this.totalStockOnHand = response.data.totalStockOnHand;
            } catch (error) {
                console.error('Error fetching total stock on hand', error);
            }
        },

        async fetchcriticalStockCount() {
            try {
                const response = await axios.get("/critical-stock-count");
                this.criticalStockCount = response.data.criticalStockCount;
            } catch (error) {
                console.error("Error fetching critical item count", error);
            }
        },
    },
};
</script>
  
<style scoped>
.card-box {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s, color 0.3s;
}

.card-box:hover {
    background-color: #134e39;
    color: #fff;
}

.chart-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 20px;
}

.chart-legend span {
    display: flex;
    align-items: center;
    margin-right: 10px;
}

.chart-legend span:before {
    content: '';
    display: inline-block;
    width: 15px;
    height: 15px;
    margin-right: 5px;
    border-radius: 50%;
}
</style>
  