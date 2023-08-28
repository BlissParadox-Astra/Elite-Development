<template>
  <div>
    <v-app>
      <StockEntrySection />
    </v-app>
  </div>
</template>
  
<script>
import StockEntrySection from '@/components/sections/Admin/StockEntrySection.vue';

export default {
  name: 'StockEntryView',
  components: {
    StockEntrySection
  },
};
</script>
