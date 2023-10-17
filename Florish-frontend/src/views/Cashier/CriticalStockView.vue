<template>
    <div>
      <v-app>
          <CriticalStock />   
      </v-app>
    </div>
  </template>
  
  <script>
  import CriticalStock from '@/components/sections/Cashier/CriticalStockSection.vue';
  export default {
    name: 'CriticalStockView',
    components: {
        CriticalStock,
    },
  };
  </script>a