<template>
    <div>
      <v-app>
          <SoldPurchase />   
      </v-app>
    </div>
  </template>
  
  <script>
  import SoldPurchase from '@/components/sections/Cashier/SoldPurchaseSection.vue';
  export default {
    name: 'SoldPurchaseView',
    components: {
        SoldPurchase,
    },
  };
  </script>a