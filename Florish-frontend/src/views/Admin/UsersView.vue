<template>
  <div>
    <v-app>
      <UsersSection />
    </v-app>
  </div>
</template>
  
<script>
import UsersSection from '@/components/sections/Admin/UsersSection.vue';

export default {
  name: 'UserView',
  components: {
    UsersSection
  },
};
</script>
