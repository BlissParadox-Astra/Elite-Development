<template>
  <div>
    <v-app>
      <StockAdjustmentSection />
    </v-app>
  </div>
</template>
  
<script>
import StockAdjustmentSection from '@/components/sections/Admin/StockAdjustmentSection.vue';

export default {
  name: 'StockAdjustmentView',
  components: {
    StockAdjustmentSection,
  },
};
</script>
