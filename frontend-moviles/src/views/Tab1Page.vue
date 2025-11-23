<template>
  <ion-page>
    <ion-header class="ion-no-border header-safe-area">
      <ion-toolbar class="custom-toolbar">
        <ion-title>Panel de Control</ion-title>
        <ThemeToggle />
        <ion-buttons slot="end">
          <ion-button @click="logout" aria-label="Cerrar Sesión">
            <ion-icon slot="icon-only" :icon="logOutOutline" class="header-icon"></ion-icon>
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
              <div class="icon-box primary-light big-icon"><ion-icon :icon="easel" color="primary"></ion-icon></div>
              <div class="text-content">
                <h2>Mis Cursos y Notas</h2>
                <p>Accede a tus materias asignadas y califica a tus alumnos.</p>
              </div>
              <ion-icon :icon="chevronForward" color="medium"></ion-icon>
            </ion-card-content>
          </ion-card>
        </div>

        <div v-else class="student-view">
          <ion-card button @click="router.push('/mis-notas')" class="menu-card big-card">
            <ion-card-content class="horizontal-content">
              <div class="icon-box primary-light big-icon"><ion-icon :icon="statsChart" color="primary"></ion-icon></div>
              <div class="text-content">
                <h2>Ver Mis Calificaciones</h2>
                <p>Consulta tu promedio y notas por periodo.</p>
              </div>
              <ion-icon :icon="chevronForward" color="medium"></ion-icon>
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
import ThemeToggle from '@/components/ThemeToggle.vue';

const authStore = useAuthStore();
const router = useRouter();

const roleName = computed(() => {
  const roles: Record<number, string> = { 1: 'Administrador', 2: 'Docente', 3: 'Alumno' };
  return roles[authStore.user?.role_id as number] || 'Usuario';
});

const isAdmin = computed(() => authStore.user?.role_id === 1);
const isDocente = computed(() => authStore.user?.role_id === 2);

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
/* =========================================
   CORRECCIÓN DE ÁREA SEGURA (BARRA DE ESTADO)
   ========================================= */
.header-safe-area {
  /* 1. env(safe-area-inset-top): Estándar moderno para leer el notch/barra.
     2. 30px: Valor de respaldo (fallback) si el móvil reporta 0.
  */
  background: var(--ion-color-primary);
}

/* Asegura que el toolbar sea transparente para que se vea el fondo del header */
.custom-toolbar {
  --background: transparent;
  --color: white;
}

.header-icon {
  color: white;
}

/* =========================================
   ESTILOS DEL DASHBOARD (Con soporte Dark Mode)
   ========================================= */

/* Fondo de la página */
.dashboard-content { 
  --background: var(--ion-background-color, var(--ion-color-light)); 
}

.dashboard-container {
  padding: 20px;
  padding-top: 20px;
  max-width: 800px;
  margin: 0 auto;
}

/* Welcome Section (Tarjeta de Bienvenida) */
.welcome-section { 
  /* CAMBIO: Usamos variable para que se ponga oscura en Dark Mode */
  background: var(--ion-card-background, white); 
  
  border-radius: 16px; 
  padding: 20px; 
  box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
  margin-bottom: 25px; 
}

.user-info { display: flex; align-items: center; gap: 15px; }

.avatar-circle { 
  width: 50px; height: 50px; 
  background: var(--ion-color-primary); 
  color: white; 
  border-radius: 50%; 
  display: flex; align-items: center; justify-content: center; 
  font-weight: bold; font-size: 1.2rem; 
}

.text-info { display: flex; flex-direction: column; align-items: flex-start; }

.greeting { 
  margin: 0; font-size: 1.2rem; font-weight: 700; 
  /* CAMBIO: Texto dinámico (negro en light, blanco en dark) */
  color: var(--ion-text-color, var(--ion-color-dark)); 
}

.role-badge { margin-top: 5px; padding: 5px 10px; border-radius: 6px; }

.section-title { 
  font-size: 1.1rem; font-weight: 600; 
  color: var(--ion-color-medium); 
  margin-bottom: 15px; margin-left: 5px; 
}

/* Tarjetas de Menú */
.menu-card {
  margin: 5px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  
  /* CAMBIO: Fondo dinámico para Dark Mode */
  --background: var(--ion-card-background, white); 
  background: var(--ion-card-background, white);
  
  transition: transform 0.1s;
  aspect-ratio: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.menu-card:active { transform: scale(0.98); }

.card-content-centered {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 10px !important;
  text-align: center;
  height: 100%;
  width: 100%;
}

.menu-card ion-label { 
  font-weight: 600; 
  /* CAMBIO: Texto dinámico */
  color: var(--ion-text-color, var(--ion-color-dark)); 
  font-size: 0.9rem; 
  margin-top: 10px; 
}

.big-card { margin-bottom: 20px; aspect-ratio: auto; display: block; }
.horizontal-content { display: flex; align-items: center; padding: 20px !important; }
.text-content { flex: 1; margin-left: 15px; }

.text-content h2 { 
  margin: 0; font-size: 1.1rem; font-weight: 700; 
  /* CAMBIO: Texto dinámico */
  color: var(--ion-text-color, var(--ion-color-dark)); 
}

.text-content p { margin: 5px 0 0 0; color: var(--ion-color-medium); font-size: 0.9rem; }

.icon-box { 
  width: 50px; height: 50px; 
  border-radius: 12px; 
  display: flex; align-items: center; justify-content: center; 
  margin-bottom: 0; font-size: 24px; 
}
.big-icon { width: 60px; height: 60px; font-size: 30px; }

/* Fondos claros para iconos (se mantienen igual, funcionan bien en ambos modos) */
.primary-light { background: rgba(var(--ion-color-primary-rgb), 0.1); }
.secondary-light { background: rgba(var(--ion-color-secondary-rgb), 0.1); }
.tertiary-light { background: rgba(var(--ion-color-tertiary-rgb), 0.1); }
.warning-light { background: rgba(var(--ion-color-warning-rgb), 0.1); }
.orange-light { background: #FFF3E0; color: #FF9800; }
.teal-light { background: rgba(var(--ion-color-secondary-rgb), 0.15); }

/* Ajuste específico para el icono naranja en modo oscuro */
:host-context(body.dark) .orange-light {
  background: rgba(255, 152, 0, 0.2); /* Más suave en modo oscuro */
  color: #ffb74d;
}
</style>