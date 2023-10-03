import { createRouter, createWebHistory } from 'vue-router';
import Cookies from 'js-cookie';
import store from '../store';
import NotFoundView from '../components/common/NotFound.vue';
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
    name: 'Admin Dashboard Page',
    component: DashboardView,
    meta: { title: 'Dashboard', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-list-view',
    name: 'Product List Page',
    component: ProductListView,
    meta: { title: 'Product List', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-entry',
    name: 'Stock Entry Page',
    component: StockEntryView,
    meta: { title: 'Stock In', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-history',
    name: 'Stock History Page',
    component: StockHistoryView,
    meta: { title: 'Stock In History', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-adjustment',
    name: 'stockAdjustment',
    component: StockAdjustmentView,
    meta: { title: 'Stock Adjustment', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-adjustment-history',
    name: 'Stock Adjustment History Page',
    component: StockAdjustmentHistoryView,
    meta: { title: 'Stock Adjustment History', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/inventory-list',
    name: 'Inventory List Page',
    component: InventoryListView,
    meta: { title: 'Inventory List', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-category',
    name: 'Product Category Page',
    component: ProductCategoryView,
    meta: { title: 'Categories', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-brand',
    name: 'Product Brand Page',
    component: ProductBrandView,
    meta: { title: 'Brands', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/sales-history',
    name: 'Sales History Page',
    component: SaleHistoryView,
    meta: { title: 'Sales History', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/critical-stocks',
    name: 'Critical Stocks Page',
    component: CriticalStockView,
    meta: { title: 'Critical Stock', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/cancelled-order',
    name: 'Cancelled Order Page',
    component: CancelledOrderView,
    meta: { title: 'Cancelled Order List', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/users',
    name: 'Users Page',
    component: UsersView,
    meta: { title: 'User Details', requiresAuth: true, role: 'admin' },
  },
  //Cashier route
  {
    path: '/cashierdashboard',
    name: 'Cashier Dashboard Page',
    component: CashierDashboard,
    meta: { requiresAuth: true, role: 'cashier' },

  },
  {
    path: '/transactionCart',
    name: 'Transaction Cart Page',
    component: TransactionCart,
    meta: { requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/lowStock',
    name: 'Low Stock Page',
    component: LowStock,
    meta: { requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/soldPurchase',
    name: 'Sold Purchase page',
    component: SoldPurchase,
    meta: { requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/login',
    alias: '/',
    name: 'Login Page',
    component: Login,
    meta: { requiresAuth: false, role: 'guest' },
  },
  {
    path: '/:catchAll(.*)',
    name: 'NotFound',
    component: NotFoundView,
    meta: { title: 'Not Found' },
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.matched.some((route) => route.meta.requiresAuth)) {
    const token = Cookies.get('token');
    if (!token) {
      store.commit('setAlertMessage', `Please log in to access ${to.name}.`);
      setTimeout(() => {
        store.commit('clearAlertMessage');
      }, 5000);
      next('/login');
    } else {
      const userRole = store.getters.getUserRole;
      if (to.meta.role === 'admin' && userRole === 'Admin') {
        next();
      } else if (to.meta.role === 'cashier' && userRole === 'Cashier') {
        next();
      } else {
        const errorMessage = `As a ${userRole}, you are not allowed to navigate to ${to.name}`;
        store.commit('setAlertMessage', errorMessage);
        if (userRole === 'Admin') {
          next('/dashboard');
        } else if (userRole === 'Cashier') {
          next('/cashierdashboard');
        }
        setTimeout(() => {
          store.commit('clearAlertMessage');
        }, 5000);
      }
    }
  } else if (to.name === 'Login Page' && store.getters.isAuthenticated) {
    store.commit('setAlertMessage', `You are already logged in, Log out first to access ${to.name}`);
    setTimeout(() => {
      store.commit('clearAlertMessage');
    }, 5000);
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