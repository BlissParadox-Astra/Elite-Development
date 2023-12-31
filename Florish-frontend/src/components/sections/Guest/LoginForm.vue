<template>
  <v-row class="content-container fill-height">
    <v-col class="mt-sm-10" cols="12" sm="12" md="6" xl="6" lg="5">
      <div class="logo">
        <v-img src="../../../assets/assets/florish-logo.png" alt="storelogo" contain></v-img>
      </div>
    </v-col>
    <v-col cols="12" sm="12" md="4" xl="6" lg="6">
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
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';
import Cookies from 'js-cookie';

export default {
  data() {
    return {
      visible: false,
      username: '',
      password: '',
      loginError: false,
      errorMessage: '',
    };
  },
  methods: {
    ...mapActions(['setAdminStatus', 'setCashierStatus']),
    async login() {
      try {
        if (!this.username || !this.password) {
          return;
        }

        const response = await axios.post('/login', {
          username: this.username,
          password: this.password,
        });

        const token = response.data.token;
        const userType = response.data.userType;
        const user = response.data.user;

        Cookies.set('token', token, { expires: 1 });

        if (Cookies.get('token')) {
          this.$store.commit('setToken', { token, userType, user });
          if (userType === 'Admin') {
            this.$store.dispatch('setAdminStatus', true);
            this.$router.push('/admin-dashboard');
          } else if (userType === 'Cashier') {
            this.$store.dispatch('setCashierStatus', true);
            this.$router.push('/cashier-dashboard');
          } else {
            console.error('Invalid user type:', userType);
            this.errorMessage = 'Invalid user type';
          }
        } else {
          console.error('Token not stored in cookies');
          this.loginError = true;
          this.errorMessage = 'Token not stored in cookies';
        }
      } catch (error) {
        this.loginError = true;
        if (error.response) {
          if (error.response.status === 401) {
            console.error('Invalid credentials');
            this.errorMessage = 'Invalid credentials';
          } else if (error.response.status === 422) {
            console.error('Validation error:', error.response.data);
          } else {
            console.error('Login error:', error);
          }
        } else {
          console.error('Login error:', error);
        }
        this.password = '';
      }
    },
  },
};
</script>

<style scoped>
.fill-height {
  overflow: hidden;
}

.content-container {
  background-image: url("../../../assets/assets/store-background.png");
  background-size: cover;
  background-position: center;
  margin-top: 64px;
  height: 100vh !important;
}

.logo {
  max-width: 100%;
  height: auto;
  margin-bottom: 20px;
}

.login-card {
  margin: 0 auto;
  cursor: default;
  margin-top: 20%;
  max-width: 90%;
  width: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  padding: 40px;
  border-radius: 8px;
}

@media only screen and (max-width: 900px) {
  .fill-height {
    overflow: auto;
  }
}
</style>
