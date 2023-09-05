import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';

const store = createStore({
    plugins: [createPersistedState()],
  state: {
    isAdmin: false, // Set the initial value of isAdmin
 
  },
  mutations: {
    setIsAdmin(state, value) {
      state.isAdmin = value;
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
