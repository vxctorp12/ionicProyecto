<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar color="primary">
        <ion-title>Mi Perfil</ion-title>
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
              <ion-item button :detail="true" class="option-item">
                <ion-icon :icon="settingsOutline" slot="start" color="medium"></ion-icon>
                <ion-label>Configuración</ion-label>
              </ion-item>
              
              <ion-item button :detail="true" class="option-item">
                <ion-icon :icon="moonOutline" slot="start" color="medium"></ion-icon>
                <ion-label>Modo Oscuro (Pronto)</ion-label>
              </ion-item>

              <ion-item button @click="logout" class="option-item logout-item">
                <ion-icon :icon="logOutOutline" slot="start" color="danger"></ion-icon>
                <ion-label color="danger">Cerrar Sesión</ion-label>
              </ion-item>
            </ion-list>

          </ion-card-content>
        </ion-card>

        <p class="version-text">Portal Estudiantil v1.0</p>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { settingsOutline, moonOutline, logOutOutline } from 'ionicons/icons';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, 
  IonCard, IonCardContent, IonBadge, IonList, IonItem, IonIcon, IonLabel 
} from '@ionic/vue';

const authStore = useAuthStore();
const router = useRouter();

// Roles y Colores
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

const logout = () => {
  authStore.logout();
  router.replace('/login');
};
</script>

<style scoped>
.profile-bg { --background: #F4F6F8; }
.profile-container { padding: 20px; display: flex; flex-direction: column; align-items: center; }

.profile-card {
  width: 100%;
  max-width: 400px;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  background: white;
  margin-top: 20px;
}

.avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
}

.avatar-circle {
  width: 80px;
  height: 80px;
  background: var(--ion-color-primary);
  color: white;
  font-size: 32px;
  font-weight: bold;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
  box-shadow: 0 4px 10px rgba(var(--ion-color-primary-rgb), 0.3);
}

.avatar-section h2 { margin: 5px 0; font-weight: 700; color: #1a1a1a; }
.email { margin: 0 0 10px 0; color: #888; font-size: 0.9rem; }
.role-pill { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; }

.divider { height: 1px; background: #F0F0F0; margin: 15px 0; width: 100%; }

.option-item { --padding-start: 0; }
.logout-item { margin-top: 10px; }
.version-text { margin-top: 20px; color: #BBB; font-size: 12px; }
</style>