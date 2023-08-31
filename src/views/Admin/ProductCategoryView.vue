<template>
  <div>
    <v-app>
      <ProductCategorySection />
    </v-app>
  </div>
</template>
  
<script>
import ProductCategorySection from '@/components/sections/Admin/ProductCategorySection.vue';

export default {
  name: 'ProductCategoryView',
  components: {
    ProductCategorySection
  },
};
</script>
