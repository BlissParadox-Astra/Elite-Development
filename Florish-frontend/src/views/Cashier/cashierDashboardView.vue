<template>
  <div>
    <v-app>
      <v-main>
        <CashierDashboard />
      </v-main>
    </v-app>
  </div>
</template>
  
<script>
import CashierDashboard from '@/components/sections/Cashier/CashierDashboardSection.vue';
export default {
  name: 'CashierDashboardView',
  components: {
    CashierDashboard,
  },
};
</script>