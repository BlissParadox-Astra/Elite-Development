<template>
    <div>
      <v-app>
          <transactionCart />   
      </v-app>
    </div>
  </template>
  
  <script>
  import transactionCart from '@/component/section/Cashier/transactionCart.vue';
  export default {
    name: 'transactionCart',
    components: {
        transactionCart,
    },
  };
  </script>