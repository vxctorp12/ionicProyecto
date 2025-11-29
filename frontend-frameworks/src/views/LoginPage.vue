<template>
  <v-container class="fill-height login-container" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="elevation-12 login-card pa-6">
          <v-card-text class="text-center">
            <h1 class="text-h4 font-weight-bold mb-2 text-white">Portal Estudiantil</h1>
            <p class="text-subtitle-1 text-medium-emphasis mb-6">Bienvenido, por favor inicia sesión.</p>

            <v-form @submit.prevent="handleLogin">
              <div class="text-left mb-1">
                <label class="text-caption font-weight-bold ml-1">Correo Electrónico</label>
              </div>
              <v-text-field
                v-model="email"
                placeholder="ejemplo@correo.com"
                name="email"
                type="email"
                variant="outlined"
                bg-color="#111827"
                color="primary"
                required
                class="mb-2"
              ></v-text-field>

              <div class="text-left mb-1">
                <label class="text-caption font-weight-bold ml-1">Contraseña</label>
              </div>
              <v-text-field
                v-model="password"
                placeholder="Ingresa tu contraseña"
                name="password"
                type="password"
                variant="outlined"
                bg-color="#111827"
                color="primary"
                required
                class="mb-4"
              ></v-text-field>

              <v-btn
                block
                color="primary"
                size="large"
                type="submit"
                :loading="loading"
                class="text-capitalize font-weight-bold mb-4"
                height="50"
              >
                Acceder
              </v-btn>

              <div class="text-center">
                <a href="#" class="text-decoration-none text-caption text-medium-emphasis">¿Olvidaste tu contraseña?</a>
              </div>
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
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
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
  background-color: #0F172A; /* Match theme background */
}
.login-card {
  background-color: #1E293B !important; /* Match theme surface */
  border: 1px solid #334155;
  border-radius: 16px;
}
</style>
