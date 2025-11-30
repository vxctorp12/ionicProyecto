<template>
  <ion-page>
    <ion-content class="login-content" :fullscreen="true">
      <div class="login-container">
        
        <ion-card class="login-card">
          <ion-card-content class="ion-padding">
            <div class="logo-container">
              <div class="logo-box">
                <ion-icon :icon="school" color="light"></ion-icon>
              </div>
              <h1 class="title">Portal Estudiantil</h1>
              <p class="subtitle">Bienvenido de nuevo</p>
            </div>
            
            <form @submit.prevent="handleLogin">
              <div class="input-group">
                <label class="input-label">Correo Institucional</label>
                <div class="input-wrapper">
                  <ion-icon :icon="mail" class="input-icon"></ion-icon>
                  <ion-input 
                    v-model="email" 
                    type="email" 
                    placeholder="ejemplo@universidad.edu" 
                    class="custom-input"
                    required
                  ></ion-input>
                </div>
              </div>
              
              <div class="input-group">
                <label class="input-label">Contraseña</label>
                <div class="input-wrapper">
                  <ion-icon :icon="lockClosed" class="input-icon"></ion-icon>
                  <ion-input 
                    v-model="password" 
                    :type="showPassword ? 'text' : 'password'" 
                    placeholder="********" 
                    class="custom-input"
                    required
                  ></ion-input>
                  <ion-icon 
                    :icon="showPassword ? eyeOff : eye" 
                    class="eye-icon"
                    @click="showPassword = !showPassword"
                  ></ion-icon>
                </div>
              </div>
              
              <div class="forgot-pass">
                <a href="#">¿Olvidaste tu contraseña?</a>
              </div>
              
              <ion-button expand="block" type="submit" class="login-btn" :disabled="isLoading">
                <span v-if="!isLoading">INGRESAR</span>
                <ion-spinner v-else name="crescent"></ion-spinner>
              </ion-button>
            </form>
          </ion-card-content>
        </ion-card>
        
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { school, mail, lockClosed, eye, eyeOff } from 'ionicons/icons';
import { IonPage, IonContent, IonCard, IonCardContent, IonInput, IonButton, IonIcon, IonSpinner, toastController } from '@ionic/vue';

const authStore = useAuthStore();
const router = useRouter();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);

const handleLogin = async () => {
  if (!email.value || !password.value) return;
  isLoading.value = true;
  try {
    await authStore.login({ email: email.value, password: password.value });
    router.replace('/tabs/tab1');
    const toast = await toastController.create({
      message: `Bienvenido, ${authStore.user?.name}`,
      duration: 2000,
      color: 'success',
      position: 'top'
    });
    await toast.present();
  } catch (error: any) {
    let message = 'Error desconocido';
    if (error.response) {
      message = `Error del servidor: ${error.response.status}`;
      if (error.response.status === 401) message = 'Credenciales incorrectas (401)';
      if (error.response.status === 500) message = 'Error interno del servidor (500)';
    } else if (error.request) {
      message = 'No hay respuesta del servidor. Verifique su conexión.';
    } else {
      message = `Error: ${error.message}`;
    }
    const toast = await toastController.create({
      message,
      duration: 5000,
      color: 'danger',
      position: 'bottom'
    });
    await toast.present();
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
/* --- 1. Fondo y Layout --- */
.login-content {
  /* Usa el fondo global (#F7F7F7 en light, #121212 en dark) */
  --background: var(--ion-background-color);
}

.login-container { 
  display: flex; 
  justify-content: center; 
  align-items: flex-start; /* FIX: Alineación superior para evitar saltos */
  min-height: 100%; 
  padding: 20px;
  padding-top: 10vh; /* FIX: Espacio superior fijo */
}

/* --- 2. Tarjeta (Card) --- */
.login-card { 
  width: 100%; 
  /* Responsive sizing matching Vuetify breakpoints */
  /* xs (< 600px) -> 100% (default) */
  
  border-radius: 16px; 
  /* FONDO DINÁMICO: Blanco en light, #1e1e1e en dark */
  background: var(--ion-card-background, white); 
  box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
  overflow: visible; 
}

/* sm (>= 600px) -> 8/12 = 66.66% */
@media (min-width: 600px) {
  .login-card {
    width: 66.66%;
  }
}

/* md (>= 960px) -> 6/12 = 50% */
@media (min-width: 960px) {
  .login-card {
    width: 50%;
  }
}

/* lg (>= 1280px) -> 4/12 = 33.33% */
@media (min-width: 1280px) {
  .login-card {
    width: 33.33%;
  }
}

/* --- 3. Elementos Visuales --- */
.logo-container { text-align: center; margin-bottom: 30px; display: flex; flex-direction: column; align-items: center; }

.logo-box { 
  background-color: var(--ion-color-primary); 
  width: 60px; height: 60px; 
  border-radius: 12px; 
  display: flex; align-items: center; justify-content: center; 
  margin-bottom: 15px; font-size: 32px; 
  box-shadow: 0 4px 10px rgba(42,103,241,0.3); 
}

.title { 
  font-size: 22px; font-weight: 700; margin: 0;
  /* TEXTO DINÁMICO: Negro en light, Blanco en dark */
  color: var(--ion-text-color, #1a1a1a);
}

.subtitle { 
  font-size: 14px; margin-top: 5px; 
  color: var(--ion-color-medium);
}

/* --- 4. Inputs --- */
.input-group { margin-bottom: 20px; text-align: left; }

.input-label { 
  display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; 
  color: var(--ion-text-color, #333); /* Etiqueta dinámica */
}

.input-wrapper { 
  display: flex; align-items: center; 
  border: 1px solid var(--ion-color-medium-shade); /* Borde sutil basado en el tema */
  border-radius: 8px; 
  padding: 0 12px; 
  transition: all 0.3s cubic-bezier(0.25,0.8,0.25,1);
  
  /* FONDO INPUT: Se adapta al modo oscuro (#1e1e1e o similar) */
  background: var(--ion-item-background, #fff); 
}

/* Ajuste manual para modo claro: borde gris suave */
:host-context(body:not(.dark)) .input-wrapper {
    border-color: #E0E0E0;
}

.input-wrapper:focus-within { 
  border-color: var(--ion-color-primary); 
  box-shadow: 0 0 0 4px rgba(var(--ion-color-primary-rgb),0.15); 
}

.input-icon { 
  font-size: 20px; margin-right: 10px; transition: color 0.3s;
  color: var(--ion-color-medium); 
}
.input-wrapper:focus-within .input-icon { color: var(--ion-color-primary); }

.custom-input { 
  --padding-start:0; --padding-end:0; --background:transparent; 
  height:45px; font-size:16px; 
  --highlight-height:0; --highlight-color-focused:transparent; 
  
  /* TEXTO INPUT: Blanco en dark, negro en light */
  --color: var(--ion-text-color) !important; 
  --placeholder-color: var(--ion-color-medium) !important; 
  --placeholder-opacity: 0.7; 
}

/* FIX: Forzar 16px en el input nativo para evitar zoom en iOS */
:deep(.native-input) {
  font-size: 16px !important;
}

.eye-icon { 
  font-size:20px; cursor:pointer; 
  color: var(--ion-color-medium);
}

/* --- 5. Otros --- */
.forgot-pass { text-align:right; margin-bottom:25px; }
.forgot-pass a { font-size:13px; color:var(--ion-color-primary); text-decoration:none; font-weight:600; }
.login-btn { --border-radius:8px; font-weight:700; height:48px; --box-shadow:0 4px 12px rgba(42,103,241,0.3); }
</style>