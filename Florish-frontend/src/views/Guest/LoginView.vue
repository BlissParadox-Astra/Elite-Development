<template>
  <div>
    <v-app>
      <LoginSection />
    </v-app>
  </div>
</template>
  
<script>
import LoginSection from '@/components/sections/Guest/LoginForm.vue';

export default {
  name: 'LoginView',
  components: {
    LoginSection
  },
};
</script>
