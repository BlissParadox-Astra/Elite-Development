<template>
    <div>
      <v-app>
          <CriticalStock />   
      </v-app>
    </div>
  </template>
  
  <script>
  import CriticalStock from '@/component/section/Cashier/CriticalStock.vue';
  export default {
    name: 'CriticalStock',
    components: {
        transactionCart,
    },
  };
  </script>a