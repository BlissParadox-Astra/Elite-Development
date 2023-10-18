import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';
import router from './router/index';
import Cookies from 'js-cookie';

const store = createStore({
  plugins: [createPersistedState()],
  state: {
    isAdmin: false,
    token: null,
    userType: null,
    isCashier: false,
    alertMessage: null,
    isSidebarCollapsed: false,
  },
  mutations: {
    setIsAdmin(state, value) {
      state.isAdmin = value;
    },
    setIsCashier(state, value) {
      state.isCashier = value;
    },
    setToken(state, { token, userType, user }) {
      state.token = token;
      state.isAdmin = userType === 'Admin';
      state.isCashier = userType === 'Cashier';
      state.userType = userType;
      state.user = user;
    },
    setAlertMessage(state, message) {
      state.alertMessage = message;
    },
    clearAlertMessage(state) {
      state.alertMessage = null;
    },
    setIsSidebarCollapsed(state, value) {
      state.isSidebarCollapsed = value;
    },
  },
  actions: {
    setAdminStatus({ commit }, value) {
      commit('setIsAdmin', value);
    },
    setCashierStatus({ commit }, value) {
      commit('setIsCashier', value);
    },
    logout({ commit }) {
      commit('setToken', { token: null, userType: null });
      Cookies.remove('token');
      router.push({ name: 'Login Page' });
    },
  },
  getters: {
    isAdmin: state => state.isAdmin,
    isCashier: state => state.isCashier,
    isAuthenticated: (state) => state.token !== null,
    getUserRole: state => state.userType,
  },
});

export default store;
