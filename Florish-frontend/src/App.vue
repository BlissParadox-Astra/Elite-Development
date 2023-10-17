<template>
  <v-app id="inspire">
    <AdminSidebar v-model="admindrawer" v-if="!isMobileSize && showAdminSidebar" />
    <v-app-bar v-if="showAppBar">
      <v-app-bar-nav-icon v-if="isMobileSize" @click="isMobileSidebarOpen = !isMobileSidebarOpen"></v-app-bar-nav-icon>
      <v-app-bar-title class="app-bar-title">{{ appTitle }}</v-app-bar-title>
      <Profile></Profile>
    </v-app-bar>
    <MessageAlert :message="alertMessage" v-if="alertMessage" />
    <v-main>
      <MobileSidebar v-model="isMobileSidebarOpen" v-if="isMobileSize && isMobileSidebarOpen" />
      <router-view v-if="showMainContent" />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const admindrawer = ref(null);
const isMobileSidebarOpen = ref(false);

const handleResize = () => {
  if (window.innerWidth <= 700) { // Adjust the value according to your desired mobile size
    isMobileSidebarOpen.value = false;
  } else {
    isMobileSidebarOpen.value = true;
  }
};

onMounted(() => {
  handleResize(); // Call the function when mounted to set the initial value
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});
</script>

<script>
import { mapGetters } from 'vuex/dist/vuex.esm-browser.js';
import AdminSidebar from './components/commons/AdminSidebar.vue';
import MobileSidebar from './components/commons/MobileSideBar.vue';
import Profile from './components/commons/Profile.vue';
import MessageAlert from './components/commons/MessageAlert.vue';

export default {
  components: {
    AdminSidebar,
    Profile,
    MessageAlert,
    MobileSidebar
  },
  data() {
    return {
      mobiledrawer: null,
      admindrawer: null,
      isMobileSize: window.innerWidth <= 700, // Adjust the value according to your desired mobile size
      isMobileSidebarOpen: false
    };
  },
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
    showAdminSidebar() {
      return this.isAdmin && !this.isMobileSize && this.showMainContent && this.$route.name !== 'Login Page';
    },
    alertMessage() {
      return this.$store.state.alertMessage;
    },
    appTitle() {
      return this.isMobileSize ? 'Florish' : this.$route.meta.title;
    },
  },
  mounted() {
    window.addEventListener('resize', this.handleResize);
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
  },
  methods: {
    handleResize() {
      this.isMobileSize = window.innerWidth <= 700; // Adjust the value according to your desired mobile size
    },
  },
};
</script>

<style>
.align-center {
  display: flex;
  align-items: center;
}

.app-bar-title {
  text-align: center;
  justify-content: center;
  display: flex;
  flex-direction: row;
  align-items: center;
  font-weight: bold;
}
</style>