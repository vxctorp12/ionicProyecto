<template>
  <ion-page>
    <ion-header class="ion-no-border header-safe-area">
      <ion-toolbar color="primary">
        <ion-title>Perfil</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="profile-bg">
      <div class="profile-container">
        <ion-card class="profile-card">
          <ion-card-content>
            <div class="avatar-section">
              <div class="avatar-circle">
                {{ getUserInitials() }}
              </div>
              <h2>{{ authStore.user?.name }}</h2>
              <p class="email">{{ authStore.user?.email }}</p>
              <ion-badge :color="getRoleColor(authStore.user?.role_id as number)" class="role-pill">
                {{ roleName }}
              </ion-badge>
            </div>

            <div class="divider"></div>

            <ion-list lines="none">
              <ion-item button @click="abrirModalPassword" class="option-item">
                <ion-icon :icon="lockClosedOutline" slot="start" color="medium"></ion-icon>
                <ion-label>Cambiar Contraseña</ion-label>
              </ion-item>

              <ion-item button @click="logout" class="option-item logout-item">
                <ion-icon :icon="logOutOutline" slot="start" color="danger"></ion-icon>
                <ion-label color="danger">Cerrar Sesión</ion-label>
              </ion-item>
            </ion-list>

            <p class="version-text">Versión 1.0.0</p>
          </ion-card-content>
        </ion-card>
      </div>

      <ion-modal :is-open="showPasswordModal" @didDismiss="showPasswordModal = false">
        <ion-header>
          <ion-toolbar color="primary">
            <ion-title>Cambiar Contraseña</ion-title>
            <ion-buttons slot="end">
              <ion-button @click="showPasswordModal = false">Cerrar</ion-button>
            </ion-buttons>
          </ion-toolbar>
        </ion-header>
        <ion-content class="ion-padding">
          <div class="modal-header">
            <h2>Actualiza tu contraseña</h2>
            <p>Ingresa tu contraseña actual y la nueva</p>
          </div>

          <div class="form-container">
            <div class="input-group">
              <label>Contraseña Actual</label>
              <ion-input 
                v-model="formPass.current_password" 
                type="password" 
                placeholder="••••••••"
                class="custom-input"
              ></ion-input>
            </div>

            <div class="input-group">
              <label>Nueva Contraseña</label>
              <ion-input 
                v-model="formPass.new_password" 
                type="password" 
                placeholder="••••••••"
                class="custom-input"
              ></ion-input>
            </div>

            <div class="input-group">
              <label>Confirmar Nueva Contraseña</label>
              <ion-input 
                v-model="formPass.confirm_password" 
                type="password" 
                placeholder="••••••••"
                class="custom-input"
              ></ion-input>
            </div>

            <ion-button 
              expand="block" 
              @click="guardarPassword" 
              :disabled="loading"
              class="btn-save"
              color="primary"
            >
              <ion-spinner v-if="loading" name="crescent"></ion-spinner>
              <span v-else>Guardar Cambios</span>
            </ion-button>
          </div>
        </ion-content>
      </ion-modal>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { moonOutline, logOutOutline, lockClosedOutline } from 'ionicons/icons';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, 
  IonCard, IonCardContent, IonBadge, IonList, IonItem, IonIcon, IonLabel,
  IonModal, IonInput, IonButton, IonSpinner, IonButtons, toastController
} from '@ionic/vue';

const authStore = useAuthStore();
const router = useRouter();

const showPasswordModal = ref(false);
const loading = ref(false);
const formPass = ref({
  current_password: '',
  new_password: '',
  confirm_password: ''
});

const roleName = computed(() => {
  const roles: Record<number, string> = { 1: 'Administrador', 2: 'Docente', 3: 'Alumno' };
  return roles[authStore.user?.role_id as number] || 'Usuario';
});

const getRoleColor = (id: number) => {
  const colors: Record<number, string> = { 1: 'primary', 2: 'success', 3: 'tertiary' };
  return colors[id] || 'medium';
};

const getUserInitials = () => {
  return (authStore.user?.name || 'U').charAt(0).toUpperCase();
};

const abrirModalPassword = () => {
  formPass.value = { current_password: '', new_password: '', confirm_password: '' };
  showPasswordModal.value = true;
};

const guardarPassword = async () => {
  if (!formPass.value.current_password || !formPass.value.new_password) {
    mostrarToast('Todos los campos son obligatorios', 'warning');
    return;
  }
  if (formPass.value.new_password !== formPass.value.confirm_password) {
    mostrarToast('Las nuevas contraseñas no coinciden', 'danger');
    return;
  }
  if (formPass.value.new_password.length < 6) {
    mostrarToast('La contraseña debe tener al menos 6 caracteres', 'warning');
    return;
  }

  loading.value = true;

  try {
    const token = authStore.token; 

    await axios.post('/change-password', {
      current_password: formPass.value.current_password,
      new_password: formPass.value.new_password,
      new_password_confirmation: formPass.value.confirm_password
    }, {
      headers: {
        Authorization: `Bearer ${token}` 
      }
    });

    mostrarToast('Contraseña actualizada correctamente', 'success');
    showPasswordModal.value = false; 
    formPass.value = { current_password: '', new_password: '', confirm_password: '' };

  } catch (error: any) {
    console.error(error);
    const msg = error.response?.data?.message || 'Error al actualizar contraseña';
    mostrarToast(msg, 'danger');
  } finally {
    loading.value = false;
  }
};

const mostrarToast = async (msg: string, color: string) => {
  const toast = await toastController.create({
    message: msg,
    duration: 2000,
    color: color,
    position: 'top',
    cssClass: 'custom-toast'
  });
  await toast.present();
};

const logout = () => {
  authStore.logout();
  router.replace('/login');
};
</script>

<style scoped>
.profile-bg { --background: var(--ion-background-color); }
.profile-container { padding: 20px; display: flex; flex-direction: column; align-items: center; }

.profile-card {
  width: 100%;
  max-width: 400px;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  background: var(--ion-card-background, white);
  margin-top: 20px;
}

.avatar-section { display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; }
.avatar-circle {
  width: 80px; height: 80px;
  background: var(--ion-color-primary);
  color: white;
  font-size: 32px; font-weight: bold;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 10px;
  box-shadow: 0 4px 10px rgba(var(--ion-color-primary-rgb), 0.3);
}

.avatar-section h2 { margin: 5px 0; font-weight: 700; color: var(--ion-text-color); }
.email { margin: 0 0 10px 0; color: var(--ion-color-medium); font-size: 0.9rem; }
.role-pill { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; }

.divider { height: 1px; background: var(--ion-color-light); margin: 15px 0; width: 100%; }

.option-item { --padding-start: 0; }
.logout-item { margin-top: 10px; }
.version-text { margin-top: 20px; color: var(--ion-color-medium); font-size: 12px; text-align: center; }

.modal-header { text-align: center; margin-bottom: 25px; margin-top: 10px; }
.modal-header h2 { font-weight: 800; color: var(--ion-text-color); margin: 0 0 5px 0; }
.modal-header p { margin: 0; color: var(--ion-color-medium); font-size: 0.9rem; }

.form-container { padding: 0 10px; }
.input-group { margin-bottom: 15px; }
.input-group label { font-size: 0.85rem; color: var(--ion-color-medium); font-weight: 600; margin-left: 5px; display: block; margin-bottom: 5px; }

.custom-input { 
  --background: var(--ion-item-background, #F8F9FA); 
  border-radius: 10px; 
  border: 1px solid var(--ion-color-light); 
  --padding-start: 10px;
}

.btn-save { margin-top: 30px; --border-radius: 10px; font-weight: bold; height: 48px; }
</style>