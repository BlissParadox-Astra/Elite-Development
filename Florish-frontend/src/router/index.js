import { createRouter, createWebHistory } from 'vue-router';
import Cookies from 'js-cookie';
import store from '../store';
//admin
import AdminDashboardView from '../views/Admin/AdminDashboardView.vue';
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
import CancelledOrderView from '../views/Admin/CancelledOrderView.vue';
import UsersView from '../views/Admin/UsersView.vue';
//cashier
import CashierDashboardView from '../views/Cashier/CashierDashboardView.vue';
import TransactionCartView from '../views/Cashier/TransactionCartView.vue';
import LowStockView from '../views/Cashier/CriticalStockView.vue';
import SoldPurchaseView from '../views/Cashier/SoldPurchaseView.vue';
//guest
import LoginView from '../views/Guest/LoginView.vue';
//common
import NotFoundView from '../components/commons/NotFound.vue';

const routes = [
  {
    path: '/admin-dashboard',
    name: 'Admin Dashboard Page',
    component: AdminDashboardView,
    meta: { title: 'Admin Dashboard Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-list-view',
    name: 'Product List Page',
    component: ProductListView,
    meta: { title: 'Product List Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-entry',
    name: 'Stock Entry Page',
    component: StockEntryView,
    meta: { title: 'Stock In Page', requiresAuth: true, role: 'admin' },
    beforeEnter: (to, from, next) => {
      const currentDate = new Date().toISOString().split('T')[0];
      to.query.date = currentDate;
      next();
    }
  },

  {
    path: '/stock-history',
    name: 'Stock History Page',
    component: StockHistoryView,
    meta: { title: 'Stock In History Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-adjustment',
    name: 'stockAdjustment',
    component: StockAdjustmentView,
    meta: { title: 'Stock Adjustment Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/stock-adjustment-history',
    name: 'Stock Adjustment History Page',
    component: StockAdjustmentHistoryView,
    meta: { title: 'Stock Adjustment History Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/inventory-list',
    name: 'Inventory List Page',
    component: InventoryListView,
    meta: { title: 'Inventory List Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-category',
    name: 'Product Category Page',
    component: ProductCategoryView,
    meta: { title: 'Categories Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/product-brand',
    name: 'Product Brand Page',
    component: ProductBrandView,
    meta: { title: 'Brands Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/sales-history',
    name: 'Sales History Page',
    component: SaleHistoryView,
    meta: { title: 'Sales History Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/critical-stocks',
    name: 'Critical Stocks Page',
    component: CriticalStockView,
    meta: { title: 'Critical Stock Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/cancelled-order',
    name: 'Cancelled Order Page',
    component: CancelledOrderView,
    meta: { title: 'Cancelled Order List Page', requiresAuth: true, role: 'admin' },
  },

  {
    path: '/users',
    name: 'Users Page',
    component: UsersView,
    meta: { title: 'User Details Page', requiresAuth: true, role: 'admin' },
  },
  //Cashier route
  {
    path: '/cashier-dashboard',
    name: 'Cashier Dashboard Page',
    component: CashierDashboardView,
    meta: {title: 'Cashier Dashboard', requiresAuth: true, role: 'cashier' },

  },
  {
    path: '/transaction-cart',
    name: 'Transaction Cart Page',
    component: TransactionCartView,
    meta: {title: 'Transaction Cart Page', requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/low-stock',
    name: 'Low Stock Page',
    component: LowStockView,
    meta: {title: 'Low Stocks Page', requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/sold-purchase',
    name: 'Sold Purchase Page',
    component: SoldPurchaseView,
    meta: {title: 'Sold Or Purchased Page', requiresAuth: true, role: 'cashier' },
  },
  {
    path: '/login',
    alias: '/',
    name: 'Login Page',
    component: LoginView,
    meta: { title: 'Login Page', requiresAuth: false, role: 'guest' },
  },
  {
    path: '/:catchAll(.*)',
    name: 'NotFound',
    component: NotFoundView,
    meta: { title: 'Not Found', requiresAuth: false },
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = Cookies.get('token');
  const userRole = store.getters.getUserRole;

  if (to.matched.some((route) => route.meta.requiresAuth)) {
    if (!token) {
      store.commit('setAlertMessage', `Please log in to access ${to.meta.title}.`);
      setTimeout(() => {
        store.commit('clearAlertMessage');
      }, 5000);
      next('/login');
    } else if ((to.meta.role === 'admin' && userRole === 'Admin') || (to.meta.role === 'cashier' && userRole === 'Cashier')) {
      next();
    } else {
      const errorMessage = `As a ${userRole}, you are not allowed to navigate to ${to.meta.title}`;
      store.commit('setAlertMessage', errorMessage);
      if (userRole === 'Admin') {
        next('/admin-dashboard'); // Redirect to admin dashboard
      } else if (userRole === 'Cashier') {
        next('/cashier-dashboard'); // Redirect to cashier dashboard
      }
      setTimeout(() => {
        store.commit('clearAlertMessage');
      }, 5000);
    }
  } else if (to.name === 'Login Page' && store.getters.isAuthenticated) {
    store.commit('setAlertMessage', `You are already logged in. Please log out to access ${to.meta.title}`);
    setTimeout(() => {
      store.commit('clearAlertMessage');
    }, 5000);
    if (store.getters.isAdmin) {
      next('/admin-dashboard'); // Redirect to admin dashboard
    } else if (store.getters.isCashier) {
      next('/cashier-dashboard'); // Redirect to cashier dashboard
    }
  } else {
    next();
  }
})
export default router;
