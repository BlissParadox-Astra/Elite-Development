<template>
  <v-menu offset-y>
    <template v-slot:activator="{ props }">
      <v-avatar v-bind="props" size="40" class="text-center">
        <v-img src="../../assets/assets/profileIcon.png" alt="John" contain></v-img>
      </v-avatar>
      <div class="text-center mt-2">Jane Smith</div>
    </template>
    <v-card class="mx-auto" max-width="300">
      <v-avatar size="120" class="elevation-1" style="left: 70px; top: 5px;">
        <v-img src="../../assets/assets/profileIcon.png" alt="John" contain></v-img>
      </v-avatar>
      <div class="text-center mt-2">Jane Smith</div>
      <v-card-text>
        <!-- <v-card-title class="subtitle-1">{{ $auth.user.username }}</v-card-title> -->
        <v-card-subtitle class="caption">Member since August 12, 2023</v-card-subtitle>
      </v-card-text>
      <v-card-actions class="justify-center">
        <v-btn color="primary" class="mr-4">Profile</v-btn>
        <v-btn color="error" router-link to="/login">Logout</v-btn>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
// import axios from 'axios';
export default {
  name: 'UserProfile',
  data: () => ({
    drawer: null,
  }),
  // methods: {
  //   logout() {
  //     axios.post('/logout')
  //       .then(() => {
  //         this.$router.push({ name: 'login' }); 
  //       })
  //       .catch(error => {
  //         console.error('Error logging out:', error);
  //       });
  //   },
  // }
}
</script>

<style scoped>
.align-center {
  display: flex;
  align-items: center;
}

.v-avatar {
  border-radius: 50%;
}

.text-center {
  text-align: center;
  font-size: 14px;
  font-weight: bold;
}
</style>