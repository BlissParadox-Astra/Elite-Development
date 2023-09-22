<template>
  <v-app id="inspire">
    <AdminSidebar v-model="drawer" v-if="isAdmin && showMainContent" />
    <v-app-bar v-if="showAppBar">
      <v-app-bar-nav-icon v-if="showNavIcon" @click="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>{{ route.meta.title }}</v-app-bar-title>
      <Profile></Profile>
    </v-app-bar>
    <v-main>
      <router-view v-if="showMainContent" />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'
const drawer = ref(null);
const route = useRoute();
</script>

<script>
import { mapGetters } from 'vuex/dist/vuex.esm-browser.js';
import { useRoute } from 'vue-router';
import AdminSidebar from './components/common/AdminSidebar.vue';
import Profile from './components/common/Profile.vue';
export default {
  components: {
    AdminSidebar,
    Profile
  },
  data: () => ({
    drawer: null,
    user: {
      initials: 'JD',
    },
  }),
  computed: {
    ...mapGetters(['isAdmin']),
    showMainContent() {
      const routeName = this.$route.name;
      const userType = this.$store.state.userType;

      return userType === 'Admin' || (userType === 'Cashier' && routeName !== 'login') || routeName === 'login';
    },
    showAppBar() {
      return this.showMainContent && this.$route.name !== 'login';
    },
    showNavIcon() {
      return this.isAdmin && this.$route.name !== 'login' && this.$store.state.userType !== 'cashier';
    },
  }
}

</script>
<style>
.align-center {
  display: flex;
  align-items: center;
}
</style>