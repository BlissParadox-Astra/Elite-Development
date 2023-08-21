
<template>
  <div>
    <v-app>
      <v-main>
        <DashboardSection />
      </v-main>
    </v-app>
  </div>
</template>

<script>
import DashboardSection from '@/components/sections/Admin/DashboardSection.vue';


export default {
  name: 'DashboardView',
  components: {
    DashboardSection,
  },
};
</script>