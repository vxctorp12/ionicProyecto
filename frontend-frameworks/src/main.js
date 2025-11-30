import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import vuetify from './plugins/vuetify'
import { loadFonts } from './plugins/webfontloader'
import axios from 'axios'

loadFonts()

// Set base URL for all requests
// Set base URL for all requests
axios.defaults.baseURL = 'http://localhost:8000/api';

// Restore token if exists
const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const pinia = createPinia()

createApp(App)
  .use(router)
  .use(pinia)
  .use(vuetify)
  .mount('#app')
