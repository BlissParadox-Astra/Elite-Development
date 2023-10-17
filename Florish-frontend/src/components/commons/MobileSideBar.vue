<template>
    <v-navigation-drawer id="navigation-mobile" app fixed :width="drawerWidth">
      <v-row class="fill-height">
      <v-col class="no-scroll">
          <v-list dense>
            <v-list-item v-for="item in menuItems" :key="item.text">
              <v-hover>
                <v-row align="center">
                  <v-col cols="2">
                    <v-icon >{{ item.icon }}</v-icon>
                  </v-col>
                  <v-col cols="10">
                    <v-list-item-title>
                      <v-list-group v-if="item.items" >
                        <template v-slot:activator="{ props }">
                          <v-list-item v-bind="props" tabindex="0">{{ item.text }}</v-list-item>
                        </template>
                        <v-list-item v-for="subItem in item.items" :key="subItem.text" :to="subItem.route" tabindex="0" class="subitems">
                          <v-list-item-title>{{ subItem.text }}</v-list-item-title>
                        </v-list-item>
                      </v-list-group>
                      <router-link v-else :to="item.route">{{ item.text }}</router-link>
                    </v-list-item-title>
                  </v-col>
                </v-row>
              </v-hover>
            </v-list-item>
          </v-list>
        </v-col>
      </v-row>
    </v-navigation-drawer>
  </template>
  
  <script>
  export default {
    name: 'MobileSidebar',
    data() {
      return {
        open: [{ text: "Stock Entry" }],
        menuItems: [
          { text: "Dashboard", route: "/admin-dashboard", icon: "mdi-view-dashboard" },
          { text: "Product", route: "/product-list-view", icon: "mdi-package-variant-closed", },
          {
            text: "Stock Entry",
            icon: "mdi-cart",
            items: [
              { text: "Stock In", route: "/stock-entry" },
              { text: "Stock In History", route: "/stock-history" },
            ],
          },
          {
            text: "Stock Adjustment",
            icon: "mdi-poll-box",
            items: [
              { text: "Stock Adjustment", route: "/stock-adjustment" },
              { text: "Stock Adjustment History", route: "/stock-adjustment-history" },
            ]
          },
          { text: "Categories", route: "/product-category", icon: "mdi-tag-multiple" },
          { text: "Brands", route: "/product-brand", icon: "mdi-label" },
          {
            text: "Records",
            icon: "mdi-database",
            items: [
              { text: "Critical Stocks", route: "/critical-stocks" },
              { text: "Cancelled Order", route: "/cancelled-order" },
              { text: "Inventory List", route: "/inventory-list" },
            ],
          },
          { text: "Sales History", route: "/sales-history", icon: "mdi-currency-usd" },
          { text: "Users", route: "/users", icon: "mdi-account-group" },
        ],
        drawerWidth: 500, // adjust the width as needed
      };
    },
  };
  </script>
  
  <style scoped>
  a {
    color: inherit;
    text-decoration: none;
  }
  
  #navigation-mobile {
    background-image: url("../../assets/assets/vuejs.jpg");
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
  }
  
  .subitems {
  background-color: #068863;
}
  
  .v-list-item:hover {
    background-color: rgba(0, 0, 0, 0.1);
  }
  .v-navigation-drawer .v-icon {
  color: #fff;
}

/* Set the color of the text to white */
.v-navigation-drawer .v-list-item-title {
  color: #fff;
}
.no-scroll{
height: fit-content !important;
}
  </style>