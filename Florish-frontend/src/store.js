import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';
import router from './router/index';
import Cookies from 'js-cookie';

const store = createStore({
  plugins: [createPersistedState()],
  state: {
    isAdmin: false, // Set the initial value of isAdmin
    token: null,
    userType: null,
    isCashier: false,
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
      router.push({ name: 'login' });
    },
  },
  getters: {
    isAdmin: state => state.isAdmin,
    isCashier: state => state.isCashier,
    isAuthenticated: (state) => state.token !== null,
  },
});

export default store;
