<template>
     <v-container fluid>
        <h1 class="text-center mb-4">CRITICAL STOCK</h1>
        <v-row>
            <v-col v-for="n in 21" :key="n" cols="12" sm="10" md="4" lg="4">
                <v-card class=" h-100 card pa-12 rounded-xl" sm="2">
                    <h1 class="text-center mb-4">Surf Fabric Conditioner</h1>
                    <v-row class="text-center">
                        <v-col class="mb-10">
                            <p class="text-h1">99%</p>
                            <h2>CRITICAL</h2>
                        </v-col>

                    </v-row>
                    <p>5 Items Left</p>
                </v-card>
            </v-col>
        </v-row>
        <v-btn to="/cashierDashboard" class="link px-5 rounded-lg" success>BACK TO DASHBOARD</v-btn>
    </v-container>
</template>
<style scoped>
.link {
    position: fixed;
    background-color: #680c07;
    right: -90px;
    top: 50%;
    transform: rotate(-90deg);
    color: #FFF;
}

.link:hover {
    background-color: #494b42;
    text-decoration: none;
}
</style>