<template>
    <div>
      <v-app>
        <InventoryListSection />
      </v-app>
    </div>
  </template>
    
  <script>
  import InventoryListSection from '@/components/sections/Admin/InventoryListSection.vue';
  
  export default {
    name: 'InventoryListView',
    components: {
        InventoryListSection
    },
  };
  </script>
  