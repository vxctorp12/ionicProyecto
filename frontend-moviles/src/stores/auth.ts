import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    // Inicializamos con lo que haya en el almacenamiento local (por si refresca la app)
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token, // Devuelve true si hay token
  },

  actions: {
    async login(credentials: any) {
      try {
        // 1. Hacemos la petición al Login que ya probaste
        const response = await axios.post('/auth/login', credentials);
        
        // 2. Extraemos los datos (Igual que en tu Postman)
        const token = response.data.access_token;
        const user = response.data.user;

        // 3. Guardamos en el Estado (Pinia)
        this.token = token;
        this.user = user;

        // 4. Guardamos en el disco (LocalStorage) para no perder sesión al cerrar la app
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));
        
        // 5. Configuramos el header para futuras peticiones
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        return true; // Éxito
      } catch (error) {
        console.error("Error en login:", error);
        throw error; // Fallo
      }
    },

    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete axios.defaults.headers.common['Authorization'];
      window.location.href = '/login';
    }
  }
});