<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar color="primary">
        <ion-title>Panel de Control</ion-title>
        
        <ion-buttons slot="end">
          <ion-button @click="logout" aria-label="Cerrar Sesión">
            <ion-icon slot="icon-only" :icon="logOutOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="dashboard-content">
      
      <div class="dashboard-container">
        
        <div class="welcome-section ion-padding-bottom">
          <div class="user-info">
            <div class="avatar-circle">
              <span class="initials">{{ getUserInitials() }}</span>
            </div>
            <div class="text-info">
              <h1 class="greeting">Hola, {{ authStore.user?.name }}</h1>
              <ion-badge :color="getRoleColor(authStore.user?.role_id as number)" class="role-badge">
                {{ roleName }}
              </ion-badge>
            </div>
          </div>
        </div>

        <div v-if="isAdmin" class="admin-menu">
          <h2 class="section-title">Gestión Académica</h2>
          <ion-grid class="ion-no-padding">
            <ion-row>
              <ion-col size="6" size-md="4">
                <ion-card button @click="router.push('/docentes')" class="menu-card">
                  <ion-card-content class="card-content-centered">
                    <div class="icon-box primary-light"><ion-icon :icon="school" color="primary"></ion-icon></div>
                    <ion-label>Docentes</ion-label>
                  </ion-card-content>
                </ion-card>
              </ion-col>
              <ion-col size="6" size-md="4">
                <ion-card button @click="router.push('/alumnos')" class="menu-card">
                  <ion-card-content class="card-content-centered">
                    <div class="icon-box secondary-light"><ion-icon :icon="people" color="secondary"></ion-icon></div>
                    <ion-label>Alumnos</ion-label>
                  </ion-card-content>
                </ion-card>
              </ion-col>
              <ion-col size="6" size-md="4">
                <ion-card button @click="router.push('/materias')" class="menu-card">
                  <ion-card-content class="card-content-centered">
                    <div class="icon-box tertiary-light"><ion-icon :icon="library" color="tertiary"></ion-icon></div>
                    <ion-label>Materias</ion-label>
                  </ion-card-content>
                </ion-card>
              </ion-col>
              <ion-col size="6" size-md="4">
                <ion-card button @click="router.push('/grados')" class="menu-card">
                  <ion-card-content class="card-content-centered">
                    <div class="icon-box warning-light"><ion-icon :icon="layers" color="warning"></ion-icon></div>
                    <ion-label>Grados</ion-label>
                  </ion-card-content>
                </ion-card>
              </ion-col>
              <ion-col size="6" size-md="4">
                <ion-card button @click="router.push('/matriculas')" class="menu-card">
                  <ion-card-content class="card-content-centered">
                    <div class="icon-box orange-light"><ion-icon :icon="card" color="warning"></ion-icon></div>
                    <ion-label>Inscripciones</ion-label>
                  </ion-card-content>
                </ion-card>
              </ion-col>
              <ion-col size="6" size-md="4">
                <ion-card button @click="router.push('/cargas')" class="menu-card">
                  <ion-card-content class="card-content-centered">
                    <div class="icon-box teal-light"><ion-icon :icon="briefcase" color="secondary"></ion-icon></div>
                    <ion-label>Carga Académica</ion-label>
                  </ion-card-content>
                </ion-card>
              </ion-col>
            </ion-row>
          </ion-grid>
        </div>

        <div v-else-if="isDocente" class="docente-menu">
          <h2 class="section-title">Panel Docente</h2>
          
          <ion-card button @click="router.push('/mis-cursos')" class="menu-card big-card">
            <ion-card-content class="horizontal-content">
              <div class="icon-box primary-light big-icon">
                <ion-icon :icon="easel" color="primary"></ion-icon>
              </div>
              <div class="text-content">
                <h2>Mis Cursos y Notas</h2>
                <p>Accede a tus materias asignadas y califica a tus alumnos.</p>
              </div>
              <ion-icon :icon="chevronForward" color="medium"></ion-icon>
            </ion-card-content>
          </ion-card>

          </div>

        <div v-else class="student-view">
          <ion-card button @click="router.push('/mis-notas')" class="info-card">
            <ion-card-content>
              <div class="icon-box primary-light big-icon">
                <ion-icon :icon="statsChart" color="primary"></ion-icon>
              </div>
              <h3>Ver Mis Calificaciones</h3>
              <p>Consulta tu promedio y notas por periodo.</p>
            </ion-card-content>
          </ion-card>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { 
  school, people, library, layers, logOutOutline, statsChart, card, briefcase, 
  easel, chevronForward 
} from 'ionicons/icons';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, 
  IonCard, IonCardContent, IonGrid, IonRow, IonCol, IonIcon, IonLabel,
  IonButtons, IonButton, IonBadge 
} from '@ionic/vue';

const authStore = useAuthStore();
const router = useRouter();

const roleName = computed(() => {
  const roles: Record<number, string> = { 1: 'Administrador', 2: 'Docente', 3: 'Alumno' };
  return roles[authStore.user?.role_id as number] || 'Usuario';
});

// Computed para detectar roles
const isAdmin = computed(() => authStore.user?.role_id === 1);
const isDocente = computed(() => authStore.user?.role_id === 2); // <--- ESTO FALTABA

const getRoleColor = (id: number) => {
  const colors: Record<number, string> = { 1: 'primary', 2: 'success', 3: 'tertiary' };
  return colors[id] || 'medium';
};

const getUserInitials = () => {
  const name = authStore.user?.name || 'U';
  return name.charAt(0).toUpperCase();
};

const logout = () => {
  authStore.logout();
  router.replace('/login');
};
</script>

<style scoped>
.dashboard-content { --background: var(--ion-color-light); }
.dashboard-container { padding: 20px; max-width: 800px; margin: 0 auto; }

/* Welcome Section */
.welcome-section { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
.user-info { display: flex; align-items: center; gap: 15px; }
.avatar-circle { width: 50px; height: 50px; background: var(--ion-color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; }
.text-info { display: flex; flex-direction: column; align-items: flex-start; }
.greeting { margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--ion-color-dark); }
.role-badge { margin-top: 5px; padding: 5px 10px; border-radius: 6px; }

/* Menu Cards */
.section-title { font-size: 1.1rem; font-weight: 600; color: var(--ion-color-medium); margin-bottom: 15px; margin-left: 5px; }
.menu-card { margin: 5px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); --background: white; transition: transform 0.1s; }
.menu-card:active { transform: scale(0.98); }

/* Estilos Grid Admin */
.card-content-centered { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px !important; text-align: center; }
.menu-card ion-label { font-weight: 600; color: var(--ion-color-dark); font-size: 0.95rem; margin-top: 10px; }

/* Estilos Docente (Tarjeta Horizontal) */
.big-card { margin-bottom: 20px; }
.horizontal-content { display: flex; align-items: center; padding: 20px !important; }
.text-content { flex: 1; margin-left: 15px; }
.text-content h2 { margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--ion-color-dark); }
.text-content p { margin: 5px 0 0 0; color: var(--ion-color-medium); font-size: 0.9rem; }

/* Icon Boxes */
.icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 0; font-size: 24px; }
.big-icon { width: 60px; height: 60px; font-size: 30px; }

/* Colors */
.primary-light { background: rgba(var(--ion-color-primary-rgb), 0.1); }
.secondary-light { background: rgba(var(--ion-color-secondary-rgb), 0.1); }
.tertiary-light { background: rgba(var(--ion-color-tertiary-rgb), 0.1); }
.warning-light { background: rgba(var(--ion-color-warning-rgb), 0.1); }
.orange-light { background: #FFF3E0; color: #FF9800; }
.teal-light { background: rgba(var(--ion-color-secondary-rgb), 0.15); }

/* Info Card */
.info-card { text-align: center; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
.info-card h3 { margin-top: 10px; font-weight: bold; color: var(--ion-color-dark); }
</style>