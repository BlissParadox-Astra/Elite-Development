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
        <v-card-title class="title text-center">LOG IN</v-card-title>
        <v-form @submit.prevent="validate">
      <div class="text-subtitle-1 text-medium-emphasis">User Name</div>

      <v-text-field
        density="compact"
        v-model="username" label="Username"
        prepend-inner-icon="mdi-account-outline"
        variant="outlined"
        required
      ></v-text-field>

      <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
        Password

        <a
          class="text-caption text-decoration-none text-blue"
          href="#"
          rel="noopener noreferrer"
          target="_blank"
        >
          Forgot login password?</a>
      </div>

      <v-text-field
      v-model="password" label="Password"
        :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
        :type="visible ? 'text' : 'password'"
        density="compact"
        placeholder="Enter your password"
        prepend-inner-icon="mdi-lock-outline"
        variant="outlined"
        @click:append-inner="visible = !visible"
      ></v-text-field>
      <v-btn
      @click="validate"
        block
        class="mb-8"
        color="blue"
        size="large"
        variant="tonal"
      >
        Log In
      </v-btn>
      </v-form>
      
      </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
  
<script>
import { mapActions } from 'vuex';

export default {
  data() {
    return {
      visible: false,
      username: "",
      password: "",
      attempt: 3,
    };
  },
  methods: {
    ...mapActions(['setAdminStatus']),
    validate() {
      if (
        (this.username === "Admin" && this.password === "password") ||
        (this.username === "Cashier" && this.password === "password")
      ) {
        alert("Password Remembered for the next login.");

        this.setAdminStatus(this.username === "Admin");

        const dashboardPath = this.username === "Admin" ? "/" : "/users";
        this.$router.push(dashboardPath); // Use Vue Router to navigate

      } else {
        this.attempt--;
        alert(`You have ${this.attempt} attempt(s) left.`);
        if (this.attempt === 0) {
          this.disableFields();
        }
      }
    },
    disableFields() {
      this.$refs.form.reset();
      this.$refs.form.resetValidation();
      this.username = "";
      this.password = "";
      this.attempt = 3;
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
  