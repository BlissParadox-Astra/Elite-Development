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
    meta: { title: 'Dashboard', requiresAuth: true },
  },

  {
    path: '/product-list-view',
    name: 'productListView',
    component: ProductListView,
    meta: { title: 'Product List', requiresAuth: true },
  },

  {
    path: '/stock-entry',
    name: 'stockEntry',
    component: StockEntryView,
    meta: { title: 'Stock In', requiresAuth: true },
  },

  {
    path: '/stock-history',
    name: 'stockHistory',
    component: StockHistoryView,
    meta: { title: 'Stock In History',requiresAuth: true },
  },

  {
    path: '/stock-adjustment',
    name: 'stockAdjustment',
    component: StockAdjustmentView,
    meta: { title: 'Stock Adjustment', requiresAuth: true },
  },

  {
    path: '/stock-adjustment-history',
    name: 'stockAdjustmentHistory',
    component: StockAdjustmentHistoryView,
    meta: { title: 'Stock Adjustment History', requiresAuth: true },
  },

  {
    path: '/inventory-list',
    name: 'inventoryList',
    component: InventoryListView,
    meta: { title: 'Inventory List', requiresAuth: true },
  },

  {
    path: '/product-category',
    name: 'productCategory',
    component: ProductCategoryView,
    meta: { title: 'Categories', requiresAuth: true },
  },

  {
    path: '/product-brand',
    name: 'productBrand',
    component: ProductBrandView,
    meta: { title: 'Brands', requiresAuth: true },
  },

  {
    path: '/sales-history',
    name: 'salesHistory',
    component: SaleHistoryView,
    meta: { title: 'Sales History', requiresAuth: true },
  },

  {
    path: '/critical-stocks',
    name: 'criticalStocks',
    component: CriticalStockView,
    meta: { title: 'Critical Stock', requiresAuth: true },
  },

  {
    path: '/cancelled-order',
    name: 'cancelledOrder',
    component: CancelledOrderView,
    meta: { title: 'Cancelled Order List', requiresAuth: true },
  },

  {
    path: '/users',
    name: 'users',
    component: UsersView,
    meta: { title: 'User Details',  requiresAuth: true },
  },

  {
    path: '/login',
    alias: '/',
    name: 'login',
    component: Login,
    meta: { title: ' Login' },
  },
  //Cashier route
  {
    path: '/cashierdashboard',
    name: 'cashierdashboard',
    component: CashierDashboard,
    meta: { requiresAuth: true },

  },
  {
    path: '/transactionCart',
    name: 'transactionCart',
    component: TransactionCart,

  },
  {
    path: '/lowStock',
    name: 'lowStock',
    component: LowStock,

  },
  {
    path: '/soldPurchase',
    name: 'soldPurchase',
    component: SoldPurchase,

  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});


router.beforeEach((to, from, next) => {
  if (to.matched.some((route) => route.meta.requiresAuth)) {
    const token = Cookies.get('token');

    if (token) {
      next();
    } else {
      next('/login');
    }
  } else if (to.name === 'login' && store.getters.isAuthenticated) {
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