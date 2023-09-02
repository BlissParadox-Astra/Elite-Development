<template>
    <v-container class="section2">
        <v-container>
            <v-row class="d-flex justify-center">
                <v-col cols="12">
                    <h2 class="text-center">Welcome! Admin Name</h2>
                </v-col>
            </v-row>
            <v-row class="text-center">
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>DAILY SALES</v-card-title>
                        <v-card-text>6,367.30 PHP</v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>PRODUCT LINE</v-card-title>
                        <v-card-text>15 PRODUCT LINES</v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>STOCK ON HAND</v-card-title>
                        <v-card-text>500 STOCKS</v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card class="card-box">
                        <v-card-title>CRITICAL ITEMS</v-card-title>
                        <v-card-text>03 ITEMS</v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <v-row class="d-flex justify-center">
                <v-col cols="12" xs="12" sm="8">
                    <v-card>
                        <v-card-text class="text-center">
                            <v-row>
                                <v-col cols="8">
                                    <canvas ref="myPieChart" width="400" height="400"></canvas>
                                </v-col>
                                <v-col cols="4">
                                    <v-row class="chart-legend">
                                        <v-col v-for="item in dataByYear" :key="item.year" cols="12"
                                            class="d-flex align-center">
                                            <v-icon :style="{ color: item.color }" class="mr-2">
                                                mdi-circle-medium
                                            </v-icon>
                                            <v-col cols="6">{{ item.year }}</v-col>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-container>
</template>
  
<script>
import Chart from "chart.js/auto";

export default {
    name: "DashboardSection",

    data() {
        return {
            dataByYear: [
                { year: 2020, value: 30, color: "#ff6384" },
                { year: 2021, value: 50, color: "#36a2eb" },
                { year: 2022, value: 20, color: "#ffcd56" },
            ],
        };
    },

    mounted() {
        this.createPieChart();
    },

    methods: {
        createPieChart() {
            const ctx = this.$refs.myPieChart.getContext("2d");

            new Chart(ctx, {
                type: "pie",
                data: {
                    labels: this.dataByYear.map((item) => item.year),
                    datasets: [
                        {
                            data: this.dataByYear.map((item) => item.value),
                            backgroundColor: this.dataByYear.map((item) => item.color),
                        },
                    ],
                },
                options: {
                    // ... your options ...
                },
            });
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
  