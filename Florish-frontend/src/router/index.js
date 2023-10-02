import { createRouter, createWebHistory } from 'vue-router';
import Cookies from 'js-cookie';
import store from '../store';
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
//cashier part
import CashierDashboard from '../components/sections/Cashier/CashierDashboard.vue';
import TransactionCart from '../components/sections/Cashier/transactionCart.vue';
import LowStock from '@/components/sections/Cashier/CriticalStock.vue';
import SoldPurchase from '@/components/sections/Cashier/SoldPurchase.vue';

const routes = [
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { title: 'Dashboard', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-list-view',
    name: 'productListView',
    component: ProductListView,
    meta: { title: 'Product List', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-entry',
    name: 'stockEntry',
    component: StockEntryView,
    meta: { title: 'Stock In', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-history',
    name: 'stockHistory',
    component: StockHistoryView,
    meta: { title: 'Stock In History',requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-adjustment',
    name: 'stockAdjustment',
    component: StockAdjustmentView,
    meta: { title: 'Stock Adjustment', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-adjustment-history',
    name: 'stockAdjustmentHistory',
    component: StockAdjustmentHistoryView,
    meta: { title: 'Stock Adjustment History', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/inventory-list',
    name: 'inventoryList',
    component: InventoryListView,
    meta: { title: 'Inventory List', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-category',
    name: 'productCategory',
    component: ProductCategoryView,
    meta: { title: 'Categories', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-brand',
    name: 'productBrand',
    component: ProductBrandView,
    meta: { title: 'Brands', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/sales-history',
    name: 'salesHistory',
    component: SaleHistoryView,
    meta: { title: 'Sales History', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/critical-stocks',
    name: 'criticalStocks',
    component: CriticalStockView,
    meta: { title: 'Critical Stock', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/cancelled-order',
    name: 'cancelledOrder',
    component: CancelledOrderView,
    meta: { title: 'Cancelled Order List', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/users',
    name: 'users',
    component: UsersView,
    meta: { title: 'User Details',  requiresAuth: true, role: 'admin' },
  },

  {
    path: '/login',
    alias: '/',
    name: 'login',
    component: Login,
    meta: { title: ' Login', requiresAuth: false, role: 'guest' },
  },
  //Cashier route
  {
    path: '/cashierdashboard',
    name: 'cashierdashboard',
    component: CashierDashboard,
    meta: { requiresAuth: true, role: 'cashier' },

  },
  {
    path: '/transactionCart',
    name: 'transactionCart',
    component: TransactionCart,
    meta: { requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/lowStock',
    name: 'lowStock',
    component: LowStock,
    meta: { requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/soldPurchase',
    name: 'soldPurchase',
    component: SoldPurchase,
    meta: { requiresAuth: true, role: 'cashier' },
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.matched.some((route) => route.meta.requiresAuth)) {
    const token = Cookies.get('token');
    if (!token) {
      // If no token is found, redirect to login
      next('/login');
    } else {
      // Check user's role and allow access based on the 'role' meta field
      const userRole = store.getters.getUserRole;
      if (to.meta.role === 'admin' && userRole === 'Admin') {
        next();
      } else if (to.meta.role === 'cashier' && userRole === 'Cashier') {
        next();
      } else {
        // If user's role doesn't match, display a message and redirect to their dashboard
        const errorMessage = `You are not allowed to navigate to ${to.name} route.`;
        store.commit('setAlertMessage', errorMessage);  // Set the error message in the store
        if (userRole === 'Admin') {
          next('/dashboard'); // Redirect admin to their dashboard
        } else if (userRole === 'Cashier') {
          next('/cashierdashboard'); // Redirect cashier to their dashboard
        }
      }
    }
  } else if (to.name === 'login' && store.getters.isAuthenticated) {
    // If user is authenticated and trying to access login, redirect to the dashboard
    if (store.getters.isAdmin) {
      next('/dashboard');
    } else if (store.getters.isCashier) {
      next('/cashierdashboard');
    }
  } else {
    next();
  }
});

export default router;