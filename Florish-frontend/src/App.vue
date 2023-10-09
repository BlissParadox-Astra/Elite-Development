<template>
  <v-app id="inspire">
    <AdminSidebar v-model="drawer" v-if="showSidebar" />
    <v-app-bar v-if="showAppBar">
      <v-app-bar-nav-icon v-if="showNavIcon" @click="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>{{ route.meta.title }}</v-app-bar-title>
      <Profile></Profile>
    </v-app-bar>
    <MessageAlert :message="alertMessage" v-if="alertMessage" />
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
import MessageAlert from './components/common/MessageAlert.vue';
export default {
  components: {
    AdminSidebar,
    Profile,
    MessageAlert
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

      return userType === 'Admin' || (userType === 'Cashier' && routeName !== 'Login Page') || routeName === 'Login Page' || routeName === 'NotFound';
    },
    showAppBar() {
      return this.showMainContent && this.$route.name !== 'Login Page' && this.$route.name !== 'NotFound';
    },
    showNavIcon() {
      return this.isAdmin && this.$route.name !== 'Login Page' && this.$store.state.userType !== 'cashier';
    },
    showSidebar() {
      return this.showMainContent && this.$route.name !== 'NotFound';
    },
    alertMessage() {
      return this.$store.state.alertMessage;
    },
  },
}

</script>
<style>
.align-center {
  display: flex;
  align-items: center;
}
</style>
