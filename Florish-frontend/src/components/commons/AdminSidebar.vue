<template>
  <v-navigation-drawer :class="['admin-sidebar', 'custom-bg-color', { 'collapsed': sidebarCollapsed }]"
    :width="sidebarCollapsed ? 70 : 250" floating permanent>
    <v-img src="../../assets/assets/florish-logo.png" alt="storelogo" class="logo" contain @click="toggleSidebar"></v-img>
    <v-list dense class="v-scrollbar icon-pointer" @click="expandSidebar">
      <v-list-item v-for="item in menuItems" :key="item.text" @click="handleItemClick(item)">
        <v-hover>
          <v-row align="center">
            <v-col cols="2">
              <v-icon>{{ item.icon }}</v-icon>
            </v-col>
            <v-col cols="10">
              <v-list-item-title>
                <v-list-group v-if="item.items">
                  <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props" tabindex="0">{{ item.text }}</v-list-item>
                  </template>
                  <v-list-item v-for="subItem in item.items" :key="subItem.text" :to="subItem.route" tabindex="0"
                    class="subitems">
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
        this.showMenuItems = false;
      } else {
        this.showMenuItems = true;
      }
    },
    expandSidebar() {
      this.sidebarCollapsed = false;
      this.showMenuItems = true;
    },
    handleItemClick(item) {
      if (!item.items) {
        if (this.sidebarCollapsed) {
          this.expandSidebar();
        }
        this.selectedItem = null;
        this.$router.push(item.route);
      } else {
        if (this.selectedItem === item) {
          this.selectedItem = null;
        } else {
          this.selectedItem = item;
        }
      }

      // Close the expanded list group items
      const listGroups = document.querySelectorAll('.v-list-group--active');
      listGroups.forEach((el) => {
        el.classList.remove('v-list-group--active');
      });
    },
    // Handle click event on sub-item
    handleSubItemClick(subItem, event) {
      event.stopPropagation();
      this.toggleSidebar();
    }
  },

};
</script>

<style scoped>
a {
  color: #ffff;
  text-decoration: none;

}

.v-navigation-drawer.custom-bg-color {
  /* background-image: url("../../assets/assets/vuejs.jpg"); */
  background-color: #23b78d;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.subitems {
  background-color: #068863;
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

/* Add styles to hide text when sidebar is collapsed */
.v-navigation-drawer.collapsed .v-list-item-title {
  display: none;
}

.v-navigation-drawer.collapsed .v-list-item-title i {
  margin-left: 0 !important;
}

.v-navigation-drawer .v-icon {
  color: #fff;
}

/* Set the color of the text to white */
.v-navigation-drawer .v-list-item-title {
  color: #fff;
}

.icon-pointer {
  cursor: pointer;
}
</style>