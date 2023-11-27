<template>
  <v-menu offset-y>
    <template v-slot:activator="{ props }">
      <v-avatar v-bind="props" size="40" class="text-center icon-pointer">
        <v-img src="../../assets/assets/profileIcon.png" alt="Profile" contain></v-img>
      </v-avatar>
    </template>
    <v-card class="mx-auto" max-width="300">
      <v-avatar size="120" class="elevation-1" style="left: 90px; top: 5px;">
        <v-img src="../../assets/assets/profileIcon.png" alt="Profile" contain></v-img>
      </v-avatar>
      <div class="text-center mt-2" v-if="user">{{ user.first_name }} {{ user.last_name }}</div>
      <v-card-text>
        <v-card-subtitle class="caption">
          Login this day of {{ formatDate(user.created_at) }}
        </v-card-subtitle>
      </v-card-text>
      <v-card-actions class="justify-center">
        <v-btn color="error" @click="logout">Logout</v-btn>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';
export default {
  name: 'UserProfile',
  data: () => ({
    drawer: null,
  }),
  computed: {
    user() {
      return this.$store.state.user;
    },
  },
  methods: {
    ...mapActions(['logout']),
    async logout() {
      try {
        await axios.post('/logout');
        this.$store.dispatch('logout');
        this.$router.push({ name: 'Login Page' });
      } catch (error) {
        console.error('Error logging out:', error);
      }
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
  }
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

.icon-pointer {
  cursor: pointer;
}
</style>