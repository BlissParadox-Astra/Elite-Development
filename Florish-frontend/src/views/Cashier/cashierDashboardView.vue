<template>
    <div>
      <v-app>
        <v-main>
          <cashierDashboard />
        </v-main>
      </v-app>
    </div>
  </template>
  
  <script>
  import cashierDashboard from '@/component/section/Cashier/CashierDashbord.vue';
  export default {
    name: 'cashierDashboard',
    components: {
        cashierDashboard,
    },
  };
  </script>