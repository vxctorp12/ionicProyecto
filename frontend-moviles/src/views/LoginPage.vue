<template>
  <ion-page>
    <ion-content class="login-bg">
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
import { 
  IonPage, IonContent, IonCard, IonCardContent, IonInput, IonButton, 
  IonIcon, IonSpinner, toastController 
} from '@ionic/vue';

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
      message: `Bienvenido, ${authStore.user.name}`,
      duration: 2000, color: 'success', position: 'top'
    });
    await toast.present();
  } catch (error) {
    const toast = await toastController.create({
      message: 'Credenciales incorrectas',
      duration: 3000, color: 'danger', position: 'bottom'
    });
    await toast.present();
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
/* Fondo General */
.login-bg {
  --background: #F4F6F8;
}

.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  padding: 20px;
}

.login-card {
  width: 100%;
  max-width: 400px;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  background: white;
  overflow: visible;
}

/* Logo y Títulos */
.logo-container {
  text-align: center;
  margin-bottom: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.logo-box {
  background-color: var(--ion-color-primary);
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
  font-size: 32px;
  box-shadow: 0 4px 10px rgba(42, 103, 241, 0.3);
}

.title {
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.subtitle {
  font-size: 14px;
  color: #888;
  margin-top: 5px;
}

/* --- INPUTS PERSONALIZADOS --- */
.input-group {
  margin-bottom: 20px;
  text-align: left;
}

.input-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.input-wrapper {
  display: flex;
  align-items: center;
  border: 1px solid #E0E0E0; /* Borde gris apagado inicial */
  border-radius: 8px;
  background: #FFFFFF;
  padding: 0 12px;
  /* Transición suave para el efecto del borde */
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* --- EFECTO AL HACER CLICK (FOCUS) EN TODO EL BORDE --- */
.input-wrapper:focus-within {
  /* 1. Cambiamos el color del borde a Azul Principal */
  border-color: var(--ion-color-primary); 
  
  /* 2. (Opcional) Agregamos un resplandor suave azul alrededor */
  box-shadow: 0 0 0 4px rgba(var(--ion-color-primary-rgb), 0.15); 
}

.input-icon {
  color: #999;
  font-size: 20px;
  margin-right: 10px;
  transition: color 0.3s;
}

/* Al hacer foco, también pintamos el icono de azul */
.input-wrapper:focus-within .input-icon {
  color: var(--ion-color-primary);
}

.custom-input {
  --padding-start: 0;
  --padding-end: 0;
  --background: transparent;
  height: 45px;
  font-size: 14px;

  /* --- AQUÍ QUITAMOS LA LÍNEA INFERIOR --- */
  --highlight-height: 0; /* Esto elimina la línea azul de abajo */
  --highlight-color-focused: transparent;
  
  /* --- COLORES DE TEXTO --- */
  /* Color del texto ingresado (Gris muy oscuro / Negro suave) */
  --color: #1A1A1A !important; 
  
  /* Color del placeholder (Gris claro para contraste) */
  --placeholder-color: #A0A0A0 !important;
  --placeholder-opacity: 1;
}

.eye-icon {
  color: #999;
  font-size: 20px;
  cursor: pointer;
}

/* Links y Botones */
.forgot-pass {
  text-align: right;
  margin-bottom: 25px;
}

.forgot-pass a {
  font-size: 13px;
  color: var(--ion-color-primary);
  text-decoration: none;
  font-weight: 600;
}

.login-btn {
  --border-radius: 8px;
  font-weight: 700;
  height: 48px;
  --box-shadow: 0 4px 12px rgba(42, 103, 241, 0.3);
}
</style>