<template>
  <div>
    <v-app>
      <SaleHistorySection />
    </v-app>
  </div>
</template>
  
<script>
import SaleHistorySection from '@/components/sections/Admin/SaleHistorySection.vue';

export default {
  name: 'InventoryView',
  components: {
    SaleHistorySection
  },
};
</script>
