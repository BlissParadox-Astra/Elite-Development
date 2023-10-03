<template>
    <div class="not-found">
        <h1>Page Not Found</h1>
        <p>The page you are looking for does not exist.</p>
        <router-link v-if="isAuthenticated" :to="dashboardRoute">Go to Dashboard</router-link>
        <router-link v-else :to="'/login'">Go to Login</router-link>
    </div>
</template>
  
<script>
import { mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters(['isAuthenticated', 'getUserRole']),
        dashboardRoute() {
            const userRole = this.getUserRole;
            return userRole === 'Admin' ? '/dashboard' : userRole === 'Cashier' ? '/cashierdashboard' : '/dashboard';
        },
    },
};
</script>
  
<style scoped>
.not-found {
    text-align: center;
    margin-top: 20px;
}
</style>
  