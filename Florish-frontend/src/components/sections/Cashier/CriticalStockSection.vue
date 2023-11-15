<template>
    <v-container fluid>
        <h1 class="text-center mb-4">CRITICAL STOCK</h1>
        <v-row v-if="products.length > 0">
            <v-col v-for="product in products" :key="product.id" cols="12" sm="12" md="12" lg="4">
                <v-card class="h-100 card pa-12 rounded-xl">
                    <h1 class="text-center mb-4">{{ product.description }}</h1>
                    <v-row class="text-center">
                        <v-col class="mb-10">
                            <p class="text-h1">{{ calculatePercentage(product.stock_on_hand, product.reorder_level) }}%</p>
                            <h2>CRITICAL</h2>
                        </v-col>
                    </v-row>
                    <p>{{ product.stock_on_hand }} Items Left</p>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-else>
            <v-col class="text-center" cols="12">
                <p class="no-critical-items">No critical items at the moment. Please check back later.</p>  
            </v-col>
        </v-row>
        <v-btn to="/cashier-dashboard" class="link px-5 rounded-lg" success>BACK TO DASHBOARD</v-btn>
    </v-container>
</template>
  
<script>
import axios from "axios";

export default {
    data() {
        return {
            products: [],
        };
    },

    mounted() {
        this.fetchCriticalStocks();
    },

    methods: {
        async fetchCriticalStocks() {
            try {
                const response = await axios.get("/critical-stocks");
                this.products = response.data.criticalStocks;
            } catch (error) {
                console.error("Error fetching critical stocks:", error);
            }
        },

        calculatePercentage(stock, reorderLevel) {
            const ratio = stock / reorderLevel;
            const percentage = Math.floor((1 - ratio) * 100);
            return percentage < 0 ? 0 : percentage;
        },
    },
};
</script>
  
<style scoped>
.link {
    position: fixed;
    background-color: #680c07;
    right: -90px;
    top: 50%;
    transform: rotate(-90deg) translate(0, 0) scale(1, 1);
    color: #FFF;
}

.link:hover {
    background-color: #494b42;
    text-decoration: none;
}

.no-critical-items {
    font-size: 18px;
    color: #777;
    margin-top: 20px;
    padding: 10px;
}
</style>
  