<template>
    <div>
      <v-app>
          <SoldPurchase />   
      </v-app>
    </div>
  </template>
  
  <script>
  import SoldPurchase from '@/components/sections/Cashier/SoldPurchase.vue';
  export default {
    name: 'SoldPurchase',
    components: {
        SoldPurchase,
    },
  };
  </script>a