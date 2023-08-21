<template>
    <div>
      <v-app>
        <StockInHistorySection /> 
      </v-app>
    </div>
  </template>
  
  <script>
import StockInHistorySection from '@/components/sections/Admin/StockInHistorySection.vue';
  
  export default {
    name: 'StockHistoryView',
    components: {
      StockInHistorySection
},
  };
  </script>