<template>
  <v-navigation-drawer app v-if="showSidebar"
    :class="['admin-sidebar', 'custom-bg-color', { 'collapsed': sidebarCollapsed }]" :width="sidebarCollapsed ? 70 : 250"
    floating permanent>
    <v-img src="../../assets/assets/florish-logo(2).png" alt="storelogo" class="logo" contain
      @click="toggleSidebar"></v-img>
    <v-list dense class="v-scrollbar">
      <v-list-item v-for="item in menuItems" :key="item.text">
        <v-hover>
          <v-row align="center">
            <v-col cols="2">
              <v-icon @click="expandSidebar">{{ item.icon }}</v-icon>
            </v-col>
            <v-col cols="10">
              <v-list-item-title v-if="showMenuItems" @click="toggleSidebar">
                <v-menu v-if="item.items" offset-y @click="toggleSidebar">
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
    <template v-slot:append v-if="showMenuItems">
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
      sidebarCollapsed: false,
      showMenuItems: true,
    };
  },
  methods: {
    toggleSidebar() {
    this.sidebarCollapsed = !this.sidebarCollapsed;
    this.$store.commit('setIsSidebarCollapsed', this.sidebarCollapsed);
    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
    if (this.sidebarCollapsed) {
      this.showMenuItems = false; // Hide menu items when collapsing
    } else {
      this.showMenuItems = true; // show menu items when expand
    }
  },
    expandSidebar() {
      this.sidebarCollapsed = false;
      if (this.sidebarCollapsed) {
        this.showMenuItems = false; // Hide menu items when collapsing
      } else {
        this.showMenuItems = true; // show menu items when expand
      }
    },
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

.expanded .v-list-item {
  background-color: rgba(0, 0, 0, 0.1);
}

.v-list-item:hover {
  background-color: rgba(0, 0, 0, 0.1);
}

.v-navigation-drawer.collapsed {
  width: 70px !important;
}

.v-navigation-drawer.collapsed .logo {
  margin-top: 10px;
}

.v-navigation-drawer.collapsed .v-scrollbar {
  margin-top: 10px;
  justify-content: center;
  text-align: center;
}

</style>
