import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

import { createPinia } from 'pinia';
import { IonicVue } from '@ionic/vue';

// Core Ionic CSS
import '@ionic/vue/css/core.css';
import '@ionic/vue/css/normalize.css';
import '@ionic/vue/css/structure.css';
import '@ionic/vue/css/typography.css';
import '@ionic/vue/css/padding.css';
import '@ionic/vue/css/float-elements.css';
import '@ionic/vue/css/text-alignment.css';
import '@ionic/vue/css/text-transformation.css';
import '@ionic/vue/css/flex-utils.css';
import '@ionic/vue/css/display.css';

// Theme variables
import './theme/variables.css';

// Axios defaults
axios.defaults.baseURL = 'http://192.168.0.102:8000/api';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';

const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const pinia = createPinia();

// Initialize theme store
import { useThemeStore } from '@/stores/theme';
const themeStore = useThemeStore();
themeStore.initializeTheme();

const app = createApp(App)
  .use(IonicVue)
  .use(pinia)
  .use(router);

router.isReady().then(() => {
  app.mount('#app');
});