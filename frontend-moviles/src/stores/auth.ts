import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({

    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token, 
  },

  actions: {
    async login(credentials: any) {
      try {
       
        const response = await axios.post('/auth/login', credentials);
        
       
        const token = response.data.access_token;
        const user = response.data.user;

       
        this.token = token;
        this.user = user;

        
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));
        
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        return true;
      } catch (error) {
        console.error("Error en login:", error);
        throw error; 
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