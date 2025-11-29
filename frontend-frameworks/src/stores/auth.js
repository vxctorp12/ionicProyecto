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
        async login(credentials) {
            try {
                console.log('=== LOGIN ATTEMPT ===');
                // Assuming the backend URL is on localhost:8000 based on typical Laravel setup
                axios.defaults.baseURL = 'http://localhost:8000/api';

                console.log('Credentials:', { email: credentials.email, password: '***' });

                const response = await axios.post('/auth/login', credentials);

                console.log('=== LOGIN SUCCESS ===');

                const token = response.data.access_token;
                const user = response.data.user;

                this.token = token;
                this.user = user;

                localStorage.setItem('token', token);
                localStorage.setItem('user', JSON.stringify(user));

                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

                return true;
            } catch (error) {
                console.error("=== LOGIN ERROR ===");
                console.error("Error object:", error);
                throw error;
            }
        },

        async changePassword(currentPassword, newPassword) {
            try {
                await axios.post('/change-password', {
                    current_password: currentPassword,
                    new_password: newPassword
                });
            } catch (error) {
                throw error;
            }
        },

        logout() {
            this.token = null;
            this.user = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            delete axios.defaults.headers.common['Authorization'];
        }
    }
});
