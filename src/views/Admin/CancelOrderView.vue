<template>
    <div>
      <v-app>
        <CancelOrderSection />
      </v-app>
    </div>
  </template>
  
  <script>
  import CancelOrderSection from '@/components/sections/Admin/CancelledOrderSection.vue';
  
  export default {
    name: 'CancelOrderView',
    components: {
    CancelOrderSection
},
  };
  </script>