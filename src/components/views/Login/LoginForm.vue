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
                    <v-card-text>
                        <v-form @submit.prevent="validate">
                            <v-text-field v-model="username" label="Username" required></v-text-field>
                            <v-text-field v-model="password" label="Password" type="password" required></v-text-field>
                            <v-checkbox v-model="isAdmin" label="I am an admin."></v-checkbox>
                            <v-container class="d-flex justify-center align-center">
                                <v-col cols="10" sm="5" class="d-flex justify-center align-center">
                                    <v-btn color="primary" block @click="validate">LOG IN</v-btn>
                                </v-col>
                            </v-container>
                        </v-form>
                    </v-card-text>
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
      username: "",
      password: "",
      isAdmin: false,
      attempt: 3,
    };
  },
  methods: {
    ...mapActions(['setAdminStatus']),
    validate() {
      if (
        (this.isAdmin && this.username === "Admin" && this.password === "password") ||
        (!this.isAdmin && this.username === "Cashier" && this.password === "password")
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

<style>
body,
html {
  margin: 0;
  padding: 0;
  height: 100%;
  width: 100%;
  overflow: hidden; /* Prevent scrolling if needed */
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
  margin-top: 20%; /* Adjust as needed */
  background-color: rgba(255, 255, 255, 0.8); /* Add a semi-transparent background */
  padding: 20px;
  border-radius: 8px;
}
</style>

  