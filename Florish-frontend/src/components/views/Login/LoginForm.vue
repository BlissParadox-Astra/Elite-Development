<template>
  <v-container permanent class="content-container">
    <v-row>
      <v-col cols="6">
        <div class="logo">
          <v-img src="../../../assets/assets/florish-logo(2).png" alt="storelogo" class="logo" contain></v-img>
        </div>
      </v-col>
      <v-col cols="6">
        <v-card class="login-card">
          <v-card-title class="title text-center">LOGIN</v-card-title>
          <v-form ref="loginForm" @submit.prevent="login">
            <v-alert v-if="loginError" color="error" dismissible @input="loginError = false">
              Invalid credentials
              <template #close>
                <v-btn icon @click="loginError = false">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
            </v-alert>
            <v-label class="text-subtitle-1 font-weight-bold">User Name</v-label>
            <v-text-field density="compact" v-model="username" label="Username" prepend-inner-icon="mdi-account-outline"
              variant="outlined" :rules="[v => !!v || 'Username is required']"></v-text-field>


            <v-label class="text-subtitle-1 font-weight-bold d-flex align-center justify-space-between">
              Password
              <a class="text-caption text-decoration-none text-blue" href="#" rel="noopener noreferrer" target="_blank">
                Forgot password?</a>
            </v-label>


            <v-text-field v-model="password" label="Password" :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
              :type="visible ? 'text' : 'password'" density="compact" placeholder="Enter your password"
              prepend-inner-icon="mdi-lock-outline" variant="outlined" @click:append-inner="visible = !visible"
              :rules="[v => !!v || 'Password is required']"></v-text-field>
            <v-btn type="submit" block class="mb-8" color="blue" size="large" variant="tonal"
              :disabled="!username || !password">
              Login
            </v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import { mapActions } from 'vuex';
import axios from 'axios';


export default {
  data() {
    return {
      visible: false,
      username: "",
      password: "",
      loginError: false,
    };
  },
  methods: {
    ...mapActions(['setAdminStatus']),
    async login() {
      try {
        // Validate the form fields again before submitting
        if (!this.username || !this.password) {
          return;
        }


        const response = await axios.post('/login', {
          username: this.username,
          password: this.password,
        });


        const token = response.data.token;


        this.$store.commit('setToken', token);


        this.$router.push('/dashboard');
      } catch (error) {
        this.loginError = true;
        if (error.response && error.response.status === 422) {
          console.error('Validation error:', error.response.data);
        } else {
          console.error('Login error:', error);
        }
        this.password = "";
      }
    },
  },
};
</script>


<style scoped>
body,
html {
  margin: 0;
  padding: 0;
  height: 100%;
  width: 100%;
  overflow: hidden;
  /* Prevent scrolling if needed */
}


.content-container {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background-image: url("../../../assets/assets/vuejs.jpg");
  background-size: cover;
  background-position: center;
}


.login-card {
  max-width: 400px;
  margin: 0 auto;
  margin-top: 20%;
  /* Adjust as needed */
  background-color: rgba(255, 255, 255, 0.8);
  /* Add a semi-transparent background */
  padding: 20px;
  border-radius: 8px;
}
</style>
