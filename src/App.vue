<template>
  <v-app>
    <v-main>
      <router-view v-if="showMainContent" />
    </v-main>
    <AdminSidebar v-if="isAdmin && showMainContent" />
  </v-app>
</template>

<script>
import { mapGetters } from 'vuex';
import AdminSidebar from './components/common/AdminSidebar.vue';

export default {
  components: {
    AdminSidebar,
  },
  computed: {
    ...mapGetters(['isAdmin']),
    showMainContent() {
      return this.isAdmin || this.$route.name === 'login';
    },
  },
};
</script>


<style>

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50; 
}
.section2 {
    padding-left: 40px;
    padding-top: 10px;
}

table {
  width: 100%;
  border-collapse: collapse;
}
</style>
