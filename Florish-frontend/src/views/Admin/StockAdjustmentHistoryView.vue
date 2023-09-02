<template>
    <div>
      <v-app>
        <StockAdjustmentHistorySection />
      </v-app>
    </div>
  </template>
    
  <script>
  import StockAdjustmentHistorySection from '@/components/sections/Admin/StockAdjustmentHistorySection.vue';
  
  export default {
    name: 'StockAdjustmentHistoryView',
    components: {
      StockAdjustmentHistorySection,
    },
  };
  </script>