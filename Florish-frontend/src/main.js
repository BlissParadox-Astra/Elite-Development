import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import { loadFonts } from './plugins/webfontloader';
import store from './store';
import axios from 'axios';
import Cookies from 'js-cookie';

axios.defaults.baseURL = 'http://127.0.0.1:8000/api';

axios.interceptors.request.use(config => {
  const token = Cookies.get('token'); 
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`;
  }
  return config;
});

loadFonts();

const app = createApp(App);

app.use(router);
app.use(vuetify);
app.use(store);

// Add a navigation guard to update the browser tab title
router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'Your Default Title'; // Update the browser tab title
  next();
});

app.mount('#app');
