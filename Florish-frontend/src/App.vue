<template>
  <v-app id="inspire">
    <AdminSidebar v-model="admindrawer" v-if="!isMobileSize && showAdminSidebar" />
    <v-app-bar v-if="showAppBar">
      <v-app-bar-nav-icon v-if="isAdmin && isMobileSize"
        @click="isMobileSidebarOpen = !isMobileSidebarOpen"></v-app-bar-nav-icon>
      <v-app-bar-title class="app-bar-title">{{ appTitle }}</v-app-bar-title>
      <Profile></Profile>
    </v-app-bar>
    <MessageAlert :message="alertMessage" v-if="alertMessage" />
    <v-main class="mt-n16" >
      <MobileSidebar v-model="isMobileSidebarOpen" v-if="isMobileSize && isMobileSidebarOpen && isAdmin" />
      <router-view v-if="showMainContent" />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import 'resize-observer-polyfill';

const admindrawer = ref(null);
const isMobileSidebarOpen = ref(false);
const resizeTimeout = ref(null);

const handleResize = () => {
  clearTimeout(resizeTimeout.value);
  resizeTimeout.value = setTimeout(() => {
    if (window.innerWidth <= 700) {
      isMobileSidebarOpen.value = false;
    } else {
      isMobileSidebarOpen.value = true;
    }
  }, 100);
  requestAnimationFrame(handleResize);
};

onMounted(() => {
  handleResize();
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
      isMobileSize: window.innerWidth <= 700,
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
      return this.isAdmin && !this.isMobileSize && this.showMainContent && this.$route.name !== 'Login Page' && this.$route.name !== 'NotFound';
    },
    alertMessage() {
      return this.$store.state.alertMessage;
    },
    appTitle() {
      return this.isMobileSize ? 'Florish IS with POS' : this.$route.meta.title;
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
      this.isMobileSize = window.innerWidth <= 700;
    },
  },
};
</script>

<style>
.app-bar-title {
  text-align: center;
  justify-content: center;
  display: flex;
  flex-direction: row;
  align-items: center;
  font-weight: bold;
  cursor: default;
}

.v-container {
  cursor: default;
}

.v-data-table {
  cursor: default;
}
</style>