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
import CashierDashboard from '@/component/section/Cashier/CashierDashbord.vue';
export default {
  name: 'cashierDashboard',
  components: {
    CashierDashboard,
  },
};
</script>