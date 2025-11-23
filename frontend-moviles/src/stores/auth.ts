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
        console.log('=== LOGIN ATTEMPT ===');
        console.log('API Base URL:', axios.defaults.baseURL);
        console.log('Credentials:', { email: credentials.email, password: '***' });

        const response = await axios.post('/auth/login', credentials);

        console.log('=== LOGIN SUCCESS ===');
        console.log('Response status:', response.status);
        console.log('Response data:', response.data);

        const token = response.data.access_token;
        const user = response.data.user;

        this.token = token;
        this.user = user;

        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));

        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        console.log('Token saved successfully');
        return true;
      } catch (error: any) {
        console.error("=== LOGIN ERROR ===");
        console.error("Error object:", error);
        console.error("Error response:", error.response);
        console.error("Error message:", error.message);
        console.error("Error status:", error.response?.status);
        console.error("Error data:", error.response?.data);
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