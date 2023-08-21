<template>
    <div>
      <v-app>
        <CriticalStockSection/>
      </v-app>
    </div>
  </template>
  
  <script>
  import CriticalStockSection from '@/components/sections/Admin/CriticalStockSection.vue';
  
  export default {
    name: 'CriticalStockView',
    components: {
    CriticalStockSection,
},
  };
  </script>