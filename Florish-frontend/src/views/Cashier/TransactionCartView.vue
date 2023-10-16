<template>
    <div>
      <v-app>
          <TransactionCart />
      </v-app>
    </div>
  </template>
  
  <script>
  import TransactionCart from '@/components/sections/Cashier/TransactionCartSection.vue';
  export default {
    name: 'TransactionCartView',
    components: {
      TransactionCart,
    },
  };
  </script>