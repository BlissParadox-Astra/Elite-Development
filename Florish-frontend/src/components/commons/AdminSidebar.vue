<template>
  <v-navigation-drawer app v-if="showSidebar"
    class="custom-bg-color"
    floating permanent>
    <v-img src="../../assets/assets/florish-logo.png" alt="storelogo" class="logo" contain
     ></v-img>
    <v-list dense class="v-scrollbar">
      <v-list-item v-for="item in menuItems" :key="item.text">
        <v-hover>
          <v-row align="center">
            <v-col cols="2">
              <v-icon>{{ item.icon }}</v-icon>
            </v-col>
            <v-col cols="10">
              <v-list-item-title>
                <v-menu v-if="item.items" offset-y>
                  <template v-slot:activator="{ props }" >
                    <span v-bind="props">{{ item.text }}</span>
                  </template>
                  <v-list>
                    <v-list-item v-for="subItem in item.items" :key="subItem.text" :to="subItem.route">
                      <v-list-item-title>{{ subItem.text }}</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
                <router-link v-else :to="item.route">{{ item.text }}</router-link>
              </v-list-item-title>
            </v-col>
          </v-row>
        </v-hover>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'AdminSidebar',

  computed: {
    ...mapGetters(['isAdmin']),
    showSidebar() {
      return this.isAdmin && this.$route.name !== 'login';
    },
  },

  data() {
    return {
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
     
    };
  },
};
</script>

<style scoped>
a {
  color: inherit;
  text-decoration: none;
}

.v-navigation-drawer.custom-bg-color {
  background-image: url("../../assets/assets/vuejs.jpg");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat; 
}

.expanded .v-list-item {
  background-color: rgba(0, 0, 0, 0.1);
}

.v-list-item:hover {
  background-color: rgba(0, 0, 0, 0.1);
}
</style>
