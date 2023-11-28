<template>
    <v-container class="dashboard mt-12" fluid>
        <h2 class="text-center">FLORISH CONVENIENCE STORE</h2>
        <v-row>
            <v-col cols="12" sm="12" md="6" xl="6" lg="6" class="d-flex justify-center">
                <div class="logo" justify="space-between">
                    <v-img src="../../../assets/assets/florish-logo.png" alt="storelogo" contain></v-img>
                </div>
            </v-col>
            <v-col cols="12" sm="12" md="6" xl="6" lg="6" class="cardbox-container">
                <v-row justify="space-between">
                    <v-col cols="12" sm="10" md="4">
                        <v-card class="mx-auto cardbox pa-4" max-width="200" to="/transaction-cart">
                            <v-img class="align-end text-white" height="fit-content"
                                src="../../../assets/assets/shopping-cart-xxl.png" cover></v-img>
                            <v-card-text class="text-center">NEW TRANSACTION</v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="12" sm="10" md="4">
                        <v-card class="mx-auto cardbox pa-4" max-width="200" to="/low-stock">
                            <v-img src="../../../assets/assets/negative-dynamic-xxl.png" alt="cart" class="icon"></v-img>
                            <v-card-text class="text-center">LOW STOCKS</v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="12" sm="10" md="4">
                        <v-card class="mx-auto cardbox pa-4" max-width="200" to="/sold-purchase">
                            <v-img src="../../../assets/assets/border-color-xxl.png" alt="cart" class="icon"></v-img>
                            <v-card-text class="text-center">SOLD PURCHASE</v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>
  
<script>
export default {
    name: 'cashierDashboard',
};
</script>
  
<style>
.dashboard {
    height: 100%;
    text-align: center;
    background-color: #07a278;
    /* background-color:#23b78d; */
    position: fixed;
}

.dashboard h2 {
    font-size: 24px;
    margin-top: 0;
    color: #fff7e5;
}

.cardbox-container {
    margin-top: 100px;
    align-items: center;
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

.logo {
    width: 100%;
    max-width: 500px;
    height: auto;
    margin-top: 20px;
    display: flex;
    justify-content: center;
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