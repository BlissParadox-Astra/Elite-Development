<template>
  <v-navigation-drawer id="navigation-mobile" app fixed :width="drawerWidth">
    <v-row class="fill-height">
      <v-col class="no-scroll">
        <v-hover>
          <v-list>
            <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard" :to="'/admin-dashboard'" tabindex="0"
             ></v-list-item>
            <v-list-item prepend-icon="mdi-package-variant-closed" title="Product" :to="'/product-list-view'" tabindex="0"
              ></v-list-item>
            <v-list-group value="stockEntry" class="white-title-color">
              <template v-slot:activator="{ props }">
                <v-list-item v-bind="props" prepend-icon="mdi-cart" title="Stock Entry" tabindex="0"
                ></v-list-item>
              </template>
              <v-list-item v-for="([title, route], i) in stockin" :key="i" :to="route" :value="title" :title="title"
                class="subItems" tabindex="0"></v-list-item>
            </v-list-group>
            <v-list-group value="stockAdjust" class="white-title-color">
              <template v-slot:activator="{ props }">
                <v-list-item v-bind="props" prepend-icon="mdi-poll-box" title="Stock Adjust" tabindex="0"
                 ></v-list-item>
              </template>
              <v-list-item v-for="([title, route], i) in stockAdjust" :key="i" :to="route" :value="title" :title="title"
                class="subItems" tabindex="0"></v-list-item>
            </v-list-group>
            <v-list-item prepend-icon="mdi-tag-multiple" title="Categories" :to="'/product-category'" tabindex="0"
              ></v-list-item>
            <v-list-item prepend-icon="mdi-label" title="Brands" :to="'/product-brand'" tabindex="0"
             ></v-list-item>
            <v-list-group value="records" class="white-title-color">
              <template v-slot:activator="{ props }">
                <v-list-item v-bind="props" prepend-icon="mdi-database" title="Records" tabindex="0"
                 ></v-list-item>
              </template>
              <v-list-item v-for="([title, route], i) in records" :key="i" :to="route" :value="title" :title="title"
                class="subItems" tabindex="0"></v-list-item>
            </v-list-group>
            <v-list-item prepend-icon="mdi-currency-usd" title="Sales History" :to="'/sales-history'" tabindex="0"
              ></v-list-item>
            <v-list-item prepend-icon="mdi-account-group" title="Users" :to="'/users'" tabindex="0"
              ></v-list-item>
          </v-list>
        </v-hover>
      </v-col>
    </v-row>
  </v-navigation-drawer>
</template>
  
<script>
export default {
  name: 'MobileSidebar',
  data: () => ({
    sidebarCollapsed: false,
    showMenuItems: true,
    open: [],
    stockin: [
      ['Stock in', "/stock-entry"],
      ['Stock in History', "/stock-history"],
    ],
    stockAdjust: [
      ['Stock Adjustment', "/stock-adjustment"],
      ['Stock Adjustment History', "/stock-adjustment-history"],
    ],
    records: [
      ["Critical Stocks", "/critical-stocks"],
      ['Cancelled Orders', "/cancelled-order"],
      ['Inventory List', "/inventory-list"],
    ],
    drawerWidth: 700, // adjust the width as needed
  }),

};

</script>
<style scoped>
a {
  color: #ffff;
  text-decoration: none;

}

#navigation-mobile {
  /* background-image: url("../../assets/assets/vuejs.jpg"); */
  background-color: #23b78d;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.white-title-color .v-list-group__header {
  color: white;
  /* background-color: #068863; */
}

.subItems {
  background-color: #068863;
}

.v-list-item:focus {
  background-color: #068863;
}

.v-list-item:hover {
  background-color: #068863;
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

.icon-pointer {
  cursor: pointer;
}</style>