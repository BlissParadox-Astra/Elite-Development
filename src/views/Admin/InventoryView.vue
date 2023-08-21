<template>
    <div>
      <v-app>
        <InventorySection />
      </v-app>
    </div>
  </template>
  
  <script>
  import InventorySection from '@/components/sections/Admin/InventorySection.vue';
  
  export default {
    name: 'InventoryView',
    components: {
      InventorySection,
    },
  };
  </script>