import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

import { createPinia } from 'pinia';
import { IonicVue } from '@ionic/vue';
import { useThemeStore } from '@/stores/theme'; // Importar aquí arriba es más ordenado

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

// 1. Creamos la instancia de la App
const app = createApp(App);

// 2. Creamos Pinia y la registramos INMEDIATAMENTE
const pinia = createPinia();
app.use(pinia);

// 3. AHORA que Pinia está registrada, inicializamos el ThemeStore
// Esto ya no dará error porque app.use(pinia) ocurrió arriba
const themeStore = useThemeStore();
themeStore.initializeTheme();

// 4. Registramos el resto de plugins
app.use(IonicVue);
app.use(router);

// 5. Montamos la app
router.isReady().then(() => {
  app.mount('#app');
});