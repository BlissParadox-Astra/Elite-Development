import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '../views/Admin/DashboardView.vue';
import InventoryView from '../views/Admin/InventoryView.vue';
import StockEntryView from '../views/Admin/StockEntryView.vue';
import StockHistoryView from '../views/Admin/StockInHistoryView.vue';
import StockAdjustmentView from '../views/Admin/StockAdjustmentView.vue';
import ProductCategoryView from '../views/Admin/ProductCategoryView.vue';
import ProductBrandView from '../views/Admin/ProductBrandView.vue';
import SaleHistoryView from '../views/Admin/SaleHistoryView.vue';
import CriticalStockView from '../views/Admin/CriticalStockView.vue';
import CancelledOrderView from '../views/Admin/CancelOrderView.vue';
import ProductTransactionView from '../views/Admin/ProductTransactionView.vue';
import UsersView from '../views/Admin/UsersView.vue';
import Login from '../components/views/Login/LoginForm.vue';


const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: DashboardView,
  },
  {
    path: '/inventory-view',
    name: 'inventoryView',
    component: InventoryView,
  },
  {
    path: '/stock-entry',
    name: 'stockEntry',
    component: StockEntryView,
  },
  {
    path: '/stock-history',
    name: 'stockHistory',
    component: StockHistoryView,
  },
  {
    path: '/stock-adjustment',
    name: 'stockAdjustment',
    component: StockAdjustmentView,
  },
  {
    path: '/product-category',
    name: 'productCategory',
    component: ProductCategoryView,
  },
  {
    path: '/product-brand',
    name: 'productBrand',
    component: ProductBrandView,
  },
  {
    path: '/sales-history',
    name: 'salesHistory',
    component: SaleHistoryView,
  },
  {
    path: '/critical-stocks',
    name: 'criticalStocks',
    component: CriticalStockView,
  },
  {
    path: '/cancelled-order',
    name: 'cancelledOrder',
    component: CancelledOrderView,
  },
  {
    path: '/transactions',
    name: 'productTransaction',
    component: ProductTransactionView,
  },
  {
    path: '/users',
    name: 'users',
    component: UsersView,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  console.log('Navigating to:', to.name);
  
  if (to.name === 'login') {
    console.log('Allowing access to login page');
    next();
  } else {
    const user = getUser();
    console.log('User:', user);
    
    if (user && user.isAdmin) {
      console.log('Allowing access to:', to.name);
      next();
    } else {
      console.log('Redirecting to login');
      next('/login');
    }
  }
});


function getUser() {
  // Replace this function with your logic to fetch the user's information from the authentication system
  // For example, you might use localStorage or Vuex to store user information
  return {
    isAdmin: true, // Replace with the actual value indicating if the user is an admin
    // ... other user properties ...
  };
}


export default router;