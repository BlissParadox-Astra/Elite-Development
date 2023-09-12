import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';

const store = createStore({
    plugins: [createPersistedState()],
  state: {
    isAdmin: false, // Set the initial value of isAdmin
    token: null,
 
  },
  mutations: {
    setIsAdmin(state, value) {
      state.isAdmin = value;
    },
    setToken(state, newToken) {
      state.token = newToken;
      state.isAdmin = true; // Assuming successful login means the user is an admin
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
