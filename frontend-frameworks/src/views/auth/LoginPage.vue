<template>
  <v-container class="fill-height login-container" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="login-card pa-6">
          <v-card-text class="text-center">
            <div class="logo-container">
              <div class="logo-box">
                <v-icon icon="mdi-school" color="white" size="32"></v-icon>
              </div>
              <h1 class="title">Portal Estudiantil</h1>
              <p class="subtitle">Bienvenido de nuevo</p>
            </div>

            <v-form @submit.prevent="handleLogin">
              <div class="text-left mb-1">
                <label class="input-label">Correo Institucional</label>
              </div>
              <div class="input-wrapper mb-4">
                <v-icon icon="mdi-email" class="input-icon mr-2"></v-icon>
                <input 
                  v-model="email"
                  type="email"
                  placeholder="ejemplo@universidad.edu"
                  class="custom-input"
                  required
                />
              </div>

              <div class="text-left mb-1">
                <label class="input-label">Contraseña</label>
              </div>
              <div class="input-wrapper mb-4">
                <v-icon icon="mdi-lock" class="input-icon mr-2"></v-icon>
                <input 
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="********"
                  class="custom-input"
                  required
                />
                <v-icon 
                  :icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'" 
                  class="eye-icon"
                  @click="showPassword = !showPassword"
                ></v-icon>
              </div>

              <div class="text-right mb-6">
                <a href="#" class="forgot-pass-link">¿Olvidaste tu contraseña?</a>
              </div>

              <v-btn
                block
                color="#2A67F1"
                size="large"
                type="submit"
                :loading="loading"
                class="login-btn mb-4"
                height="48"
                elevation="2"
              >
                INGRESAR
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    
    <v-snackbar v-model="snackbar" :color="snackbarColor">
      {{ snackbarText }}
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar = false">Cerrar</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('error');

const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  if (!email.value || !password.value) {
    showSnackbar('Por favor completa todos los campos', 'warning');
    return;
  }

  loading.value = true;
  try {
    await authStore.login({
      email: email.value,
      password: password.value
    });
    router.push('/dashboard');
  } catch (error) {
    showSnackbar('Credenciales incorrectas. Intenta de nuevo.', 'error');
  } finally {
    loading.value = false;
  }
};

const showSnackbar = (text, color) => {
  snackbarText.value = text;
  snackbarColor.value = color;
  snackbar.value = true;
};
</script>

<style scoped>
.login-container {
  background-color: #121212;
}

.login-card {
  background-color: #1e1e1e !important;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
}

.logo-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 30px;
}

.logo-box {
  background-color: #2A67F1;
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
  box-shadow: 0 4px 10px rgba(42,103,241,0.3);
}

.title {
  font-size: 22px;
  font-weight: 700;
  margin: 0;
  color: #ffffff;
}

.subtitle {
  font-size: 14px;
  margin-top: 5px;
  color: #bbbbbb;
}

.input-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #ffffff;
}

.input-wrapper {
  display: flex;
  align-items: center;
  border: 1px solid #5a5a5a;
  border-radius: 8px;
  padding: 0 12px;
  background: #1e1e1e;
  transition: all 0.3s;
  height: 48px;
}

.input-wrapper:focus-within {
  border-color: #2A67F1;
  box-shadow: 0 0 0 4px rgba(42,103,241,0.15);
}

.input-icon {
  color: #666666;
}

.input-wrapper:focus-within .input-icon {
  color: #2A67F1;
}

.custom-input {
  flex: 1;
  background: transparent;
  border: none;
  color: #ffffff;
  font-size: 14px;
  outline: none;
  height: 100%;
}

.custom-input::placeholder {
  color: #666666;
  opacity: 0.7;
}

.eye-icon {
  color: #666666;
  cursor: pointer;
}

.forgot-pass-link {
  font-size: 13px;
  color: #2A67F1;
  text-decoration: none;
  font-weight: 600;
}

.login-btn {
  border-radius: 8px;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(42,103,241,0.3);
  color: white !important;
}
</style>
