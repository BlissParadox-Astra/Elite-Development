<template>
  <v-app id="inspire">
    <AdminSidebar v-model="drawer" v-if="isAdmin && showMainContent" />
    <v-app-bar v-if="showAppBar">
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>{{ route.meta.title }}</v-app-bar-title>
      <v-menu>
        <template v-slot:activator="{ props }">
          <v-list-item lines="two" v-bind="props">
            <v-avatar>
              <v-img src="../src/assets/assets/profileIcon.png" alt="John"></v-img>
            </v-avatar>
            <span>Jane Smith</span>
          </v-list-item>
        </template>
        <v-card class="mx-auto" width="300">
          <v-img class="align-end text-white" height="auto" src="../src/assets/assets/florish-logo.png" cover>
          </v-img>
          <v-card-actions class="d-flex justify-space-around">
            <v-btn>Profile</v-btn>
            <v-btn>Logout</v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>
    </v-app-bar>
    <v-main>
      <router-view v-if="showMainContent" />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'
// import { useRouter } from 'vue-router'
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
  data: () => ({
    drawer: null,
    user: {
      initials: 'JD',
    },
  }),
  computed: {
    ...mapGetters(['isAdmin']),
    showMainContent() {
      return this.isAdmin || this.$route.name === 'login';
    },
    showAppBar() {
      return this.showMainContent && this.$route.name !== 'login';
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