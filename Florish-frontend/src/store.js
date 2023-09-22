import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';

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
    setToken(state, { token, userType }) {
      state.token = token;
      state.isAdmin = userType === 'Admin';
      state.isCashier = userType === 'Cashier';
      state.userType = userType;
    },
  },
  actions: {
    setAdminStatus({ commit }, value) {
      commit('setIsAdmin', value);
    },
    setCashierStatus({ commit }, value) {
      commit('setIsCashier', value);
    },
  },
  getters: {
    isAdmin: state => state.isAdmin,
    isCashier: state => state.isCashier
  },
});

export default store;
