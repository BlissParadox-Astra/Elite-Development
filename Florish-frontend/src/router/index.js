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
    meta: { title: 'Product List' },
  },

  {
    path: '/stock-entry',
    name: 'stockEntry',
    component: StockEntryView,
    meta: { title: 'Stock In' },
  },

  {
    path: '/stock-history',
    name: 'stockHistory',
    component: StockHistoryView,
    meta: { title: 'Stock In History' },
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
    meta: { title: 'Inventory List' },
  },

  {
    path: '/product-category',
    name: 'productCategory',
    component: ProductCategoryView,
    meta: { title: 'Categories' },
  },

  {
    path: '/product-brand',
    name: 'productBrand',
    component: ProductBrandView,
    meta: { title: 'Brands' },
  },

  {
    path: '/sales-history',
    name: 'salesHistory',
    component: SaleHistoryView,
    meta: { title: 'Sales History' },
  },

  {
    path: '/critical-stocks',
    name: 'criticalStocks',
    component: CriticalStockView,
    meta: { title: 'Critical Stock' },
  },

  {
    path: '/cancelled-order',
    name: 'cancelledOrder',
    component: CancelledOrderView,
    meta: { title: 'Cancelled Order List' },
  },

  {
    path: '/users',
    name: 'users',
    component: UsersView,
    meta: { title: 'User Details' },
  },
  
  {
    path: '/login',
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
export default router;
