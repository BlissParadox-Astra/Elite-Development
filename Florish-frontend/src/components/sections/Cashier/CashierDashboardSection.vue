<template>
    <v-container class="dashboard mt-15" fluid>
      <!-- <h2 class="text-center">FLORISH CONVENIENCE STORE</h2> -->
      <v-row>
        <v-col cols="12" sm="12" md="6" xl="6" lg="5" class="d-flex pa-5">
          <div class="logo">
            <v-img src="../../../assets/assets/florish-logo.png" alt="storelogo" class="logo-img" ></v-img>
          </div>
        </v-col>
        <v-col cols="12" sm="12" md="6" xl="6" lg="7" class="cardbox-container">
          <v-row justify="center" class="cards pa-10">
            <v-col cols="12" sm="10" md="6" lg="4" xl="4">
              <v-card class="mx-auto cardbox pa-4" max-width="250" to="/transaction-cart">
                <v-icon class="icon-size">mdi-cart-variant</v-icon>
                <v-card-text class="text-center name">NEW TRANSACTION</v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" sm="10" md="6" lg="4" xl="4">
              <v-card class="mx-auto cardbox pa-4" max-width="250" to="/low-stock">
                <v-icon class="icon-size">mdi-clipboard-alert-outline</v-icon>
                <v-card-text class="text-center name">LOW STOCKS</v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" sm="10" md="6" lg="4" xl="4">
              <v-card class="mx-auto cardbox pa-4" max-width="250" to="/sold-purchase">
                <v-icon class="icon-size">mdi-cash</v-icon>
                <v-card-text class="text-center name">SOLD PURCHASE</v-card-text>
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
  
  <style scoped>
  .cards {
    margin-top: 80px;
  }
  
  .d-flex {
    background-color: #07a278;
    height: 100%;
  }
  
  .name {
    color:  #07a278;
  }
  
  .cardbox-container {
    align-items: center;
    background-color: #fff;
  }
  
  .cardbox {
    color: black;
    font-size: 18px;
    text-decoration: none;
    border-radius: 16px;
    text-align: center;
    transition: background-color 0.3s;
  }
  
  .v-icon.icon-size {
    color: #07a278;
    font-size: 10vw; /* Adjust the size as needed */
    transition: color 0.3s;
  }
  
  .cardbox:hover {
    background-color: #07a278; /* Change background color on hover */
  }
  
  .cardbox:hover .v-icon.icon-size {
    color: #fff; /* Change icon color to white on hover */
  }
  
  .cardbox:hover .name {
    color: #fff; /* Change text color to white on hover */
  }
  
  .logo {
    width:100%;
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
  