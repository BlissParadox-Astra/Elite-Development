<template>
  <v-app id="inspire">
    <AdminSidebar v-model="drawer" v-if="isAdmin && showMainContent" />
    <v-app-bar>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <v-app-bar-title>{{ route.meta.title }}</v-app-bar-title>
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
import { mapGetters } from 'vuex';
import { useRoute } from 'vue-router';
import AdminSidebar from './components/common/AdminSidebar.vue';
  export default {
    components: {
    AdminSidebar,
  },
    data: () => ({ drawer: null }),
    computed: {
    ...mapGetters(['isAdmin']),
    showMainContent() {
      return this.isAdmin || this.$route.name === 'login';
    },
  }
  }
  
</script>