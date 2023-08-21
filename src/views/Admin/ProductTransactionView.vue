<template>
    <div>
      <v-app>
        <TransactionSection /> 
      </v-app>
    </div>
  </template>
  
  <script>
import TransactionSection from '@/components/sections/Admin/ProductTransactionSection.vue';
  
  export default {
    name: 'ProductTransaction',
    components: {
    TransactionSection
},
  };
  </script>