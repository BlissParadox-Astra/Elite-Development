<template>
  <v-navigation-drawer app v-if="showSidebar" class="admin-sidebar custom-bg-color" floating
        permanent>
    <v-img src="../../assets/assets/florish-logo(2).png" alt="storelogo" class="logo" contain></v-img>
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
                  <template v-slot:activator="{ props }">
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
    <template v-slot:append>
      <div class="pa-2">
        <v-btn block>
          <router-link to="/login" class="v-btn" block>
            Logout
          </router-link>
        </v-btn>
      </div>
    </template>
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
        { text: "Dashboard", route: "/", icon: "mdi-view-dashboard" },
        {
          text: "Products",
          icon: "mdi-package-variant-closed",
          items: [
            { text: "Inventory", route: "/inventory-view" },
            { text: "Stock Entry", route: "/stock-entry" },
            { text: "Stock In History", route: "/stock-history" },
            { text: "Stock Adjustment", route: "/stock-adjustment" },
          ],
        },
        { text: "Categories", route: "/product-category", icon: "mdi-tag-multiple" },
        { text: "Brands", route: "/product-brand", icon: "mdi-label" },
        {
          text: "Sales And Records",
          icon: "mdi-cart",
          items: [
            { text: "Sales", route: "/sales-history" },
            { text: "Critical Stocks", route: "/critical-stocks" },
            { text: "Cancelled Order", route: "/cancelled-order" },
          ],
        },
        { text: "Transactions", route: "/transactions", icon: "mdi-currency-usd" },
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
  background-position: center center;
  background-repeat: no-repeat;
}
</style>