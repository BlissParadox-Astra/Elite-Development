<template>
  <div >
    <v-app >
      <LoginSection />
    </v-app>
  </div>
</template>
  
<script>
import LoginSection from './components/views/Login/LoginForm.vue';


export default {
  name: 'LoginView',
  components: {
    LoginSection
  },
};
</script>