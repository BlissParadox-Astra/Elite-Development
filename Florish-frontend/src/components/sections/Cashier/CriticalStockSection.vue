<template>
    <v-container class="mt-12" fluid>
        <h1 class="text-center">CRITICAL STOCKS</h1>
        <v-row v-if="loading">
            <v-col class="text-center" cols="12">
                <p>Loading...</p>
            </v-col>
        </v-row>
        <v-row v-else-if="products.length > 0">
            <v-col v-for="product in products" :key="product.id" cols="12" sm="12" md="12" lg="4">
                <v-card class="h-100 card pa-12 rounded-xl">
                    <h1 class="text-center mb-4">{{ product.description }}</h1>
                    <v-row class="text-center">
                        <v-col class="mb-10">
                            <p class="text-h3">{{ product.stock_on_hand }} Items Left</p>
                            <h2 class="pa-">CRITICAL</h2>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-else>
            <v-col class="text-center" cols="12">
                <p class="no-critical-items">No critical items at the moment. Please check back later.</p>
            </v-col>
        </v-row>
        <v-btn to="/cashier-dashboard" class="link px-5 rounded-lg" success color="#23b78d">BACK TO DASHBOARD</v-btn>
    </v-container>
</template>
  
<script>
import axios from "axios";

export default {
    data() {
        return {
            products: [],
            loading: true,
        };
    },

    mounted() {
        this.fetchCriticalStocks();
    },

    methods: {
        async fetchCriticalStocks() {
            try {
                const response = await axios.get("/all-critical-stocks");
                this.products = response.data.criticalStocks;
            } catch (error) {
                console.error("Error fetching critical stocks:", error);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
  
<style scoped>
.link {
    position: fixed;
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

.card h1 {
    color: #23b78d;
}

.card {
    background-color: #ffff;
}

.card h2 {
    margin-top: 15px;
    color: rgb(240, 35, 35);
    font-size: 24px;
}

.card:hover {
    background-color: rgb(203, 20, 20);
}

.card:hover h2 {
    color: white;
}
</style>
  