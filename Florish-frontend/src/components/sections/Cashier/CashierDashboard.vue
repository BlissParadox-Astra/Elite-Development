<template>
   
        <v-container class="dashboard" fluid>
            <h2 class="text-center">FLORISH CONVENIENCE STORE</h2>
            <v-row justify="center" class="cardbox-container">
                <v-col cols="12" sm="6" md="4">
                    <v-card class="mx-auto cardbox pa-4" max-width="200" to='transactionCart'>
                        <v-img class="align-end text-white" height="fit-content"
                            src="../../../assets/assets/shopping-cart-xxl.png" cover>
                        </v-img>
                        <v-card-text class="text-center">NEW TRANSACTION</v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <v-card class="mx-auto cardbox pa-4" max-width="200" to="/lowStock">
                        <v-img src="../../../assets/assets/negative-dynamic-xxl.png" alt="cart" class="icon"></v-img>
                        <v-card-text class="text-center">LOW STOCKS</v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <v-row justify="center">
                <v-col cols="12" sm="6" md="4">
                    <v-card class="mx-auto cardbox pa-4" max-width="200" to="/soldPurchase">
                        <v-img src="../../../assets/assets/border-color-xxl.png" alt="cart" class="icon"></v-img>
                        <v-card-text class="text-center"> SOLD PURCHASE</v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
</template>
  
<script>
export default {
    name: 'cashierDashboard',
}
</script>
  
<style>


.dashboard {
    margin: 40px auto;
    text-align: center;
    background-color: #859766;
}

.dashboard h2 {
    font-size: 24px;
    margin-top: 0;
    color: #fff7e5;
}

.cardbox-container {
    margin-top: 20px;
}

.cardbox {
    background-color: #fff7e5;
    color: black;
    font-size: 18px;
    text-decoration: none;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.cardbox:hover {
    background-color: #494b42;
    color: #fff7e5;
}

.icon {
    max-width: 100%;
    height: auto;
}

.logout-btn {
    display: flex;
    align-items: center;
    margin-top: 20px;
    text-decoration: none;
    color: #333;
    font-size: 30px;
    font-weight: 600;
    text-align: center;
    align-items: center;
    justify-content: center;
    position: relative;
}

.logout-btn:hover {
    color: #494b42;
}

.logout-btn i {
    margin-right: 20px;
}
</style>