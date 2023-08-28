import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';

const store = createStore({
    plugins: [createPersistedState()],
  state: {
    isAdmin: false, // Set the initial value of isAdmin
    isSidebarCollapsed: false, // Add this new property
    expandSidebarCollapsed: true,
  },
  mutations: {
    setIsAdmin(state, value) {
      state.isAdmin = value;
    },
     setIsSidebarCollapsed(state, value) {
    state.isSidebarCollapsed = value;
  },
  expandSidebarCollapsed(state, value) {
    state.expandSidebarCollapsed = value;
  },
  },
  actions: {
    setAdminStatus({ commit }, value) {
      commit('setIsAdmin', value);
    },
  },
  getters: {
    isAdmin: state => state.isAdmin,
  },
});

export default store;
