import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import { loadFonts } from './plugins/webfontloader';
import store from './store';
import axios from 'axios';

axios.defaults.baseURL = 'http://127.0.0.1:8000/api';

loadFonts();

const app = createApp(App);

app
  .use(router)
  .use(vuetify)
  .use(store);

app.mount('#app');

  
