import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '../views/Admin/DashboardView.vue';
import ProductListView from '../views/Admin/ProductListView.vue';
import StockEntryView from '../views/Admin/StockEntryView.vue';
import StockHistoryView from '../views/Admin/StockInHistoryView.vue';
import StockAdjustmentHistoryView from '../views/Admin/StockAdjustmentHistoryView';
import InventoryListView from '../views/Admin/InventoryListView';
import StockAdjustmentView from '../views/Admin/StockAdjustmentView.vue';
import ProductCategoryView from '../views/Admin/ProductCategoryView.vue';
import ProductBrandView from '../views/Admin/ProductBrandView.vue';
import SaleHistoryView from '../views/Admin/SaleHistoryView.vue';
import CriticalStockView from '../views/Admin/CriticalStockView.vue';
import CancelledOrderView from '../views/Admin/CancelOrderView.vue';
import UsersView from '../views/Admin/UsersView.vue';
import Login from '../components/views/Login/LoginForm.vue';

const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: DashboardView,
    meta: { title: 'Dashboard' },
  },

  {
    path: '/product-list-view',
    name: 'productListView',
    component: ProductListView,
    meta: { title: 'Product List' },
  },

  {
    path: '/stock-entry',
    name: 'stockEntry',
    component: StockEntryView,
    meta: { title: 'STOCK IN' },
  },

  {
    path: '/stock-history',
    name: 'stockHistory',
    component: StockHistoryView,
    meta: { title: 'Product List' },
  },

  {
    path: '/stock-adjustment',
    name: 'stockAdjustment',
    component: StockAdjustmentView,
    meta: { title: 'Stock Adjustment' },
  },

  {
    path: '/stock-adjustment-history',
    name: 'stockAdjustmentHistory',
    component: StockAdjustmentHistoryView,
    meta: { title: 'Stock Adjustment History' },
  },

  {
    path: '/inventory-list',
    name: 'inventoryList',
    component: InventoryListView,
    meta: { title: 'INVENTORY LIST' },
  },

  {
    path: '/product-category',
    name: 'productCategory',
    component: ProductCategoryView,
    meta: { title: 'CATEGORIES' },
  },

  {
    path: '/product-brand',
    name: 'productBrand',
    component: ProductBrandView,
    meta: { title: 'BRAND' },
  },

  {
    path: '/sales-history',
    name: 'salesHistory',
    component: SaleHistoryView,
    meta: { title: 'SALES HISTORY' },
  },

  {
    path: '/critical-stocks',
    name: 'criticalStocks',
    component: CriticalStockView,
    meta: { title: 'CRITICAL STOCK' },
  },

  {
    path: '/cancelled-order',
    name: 'cancelledOrder',
    component: CancelledOrderView,
    meta: { title: 'CANCELLED ORDER LIST' },
  },

  {
    path: '/users',
    name: 'users',
    component: UsersView,
    meta: { title: 'USER DETAILS' },
  },
  
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { title: ' Login' },
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
