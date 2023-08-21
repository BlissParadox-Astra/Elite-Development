<template>
    <div>
      <v-app>
        <ProductBrandSection />
      </v-app>
    </div>
  </template>
  
  <script>
 
import ProductBrandSection from '@/components/sections/Admin/ProductBrandSection.vue';
  
  export default {
    name: 'ProductBrandView',
    components: {

    ProductBrandSection
},
  };
  </script>