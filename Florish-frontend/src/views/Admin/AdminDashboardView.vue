<template>
  <div>
    <v-app>
      <v-main>
        <AdminDashboardSection />
      </v-main>
    </v-app>
  </div>
</template>

<script>
import AdminDashboardSection from '@/components/sections/Admin/AdminDashboardSection.vue';

export default {
  name: 'DashboardView',
  components: {
    AdminDashboardSection,
  },
};
</script>
