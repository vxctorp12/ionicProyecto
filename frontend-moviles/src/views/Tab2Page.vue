<template>
  <ion-page>
    <ion-header class="ion-no-border header-safe-area">
      <ion-toolbar color="primary">
        <ion-title>{{ pageTitle }}</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <div v-else-if="isDocente">
        <ion-list class="custom-list">
          <ion-list-header>
            <ion-label>Tus Materias Asignadas</ion-label>
          </ion-list-header>

          <ion-card v-for="carga in items" :key="carga.id" class="course-card" button @click="router.push('/mis-cursos')">
            <ion-card-content>
              <div class="card-flex">
                <div class="icon-box bg-blue">
                  <ion-icon :icon="book" color="light"></ion-icon>
                </div>
                <div class="info-box">
                  <h2>{{ carga.materia?.nombre }}</h2>
                  <p class="grade-text">{{ carga.materia?.grado?.nombre }}</p>
                </div>
                <ion-icon :icon="chevronForward" color="medium"></ion-icon>
              </div>
            </ion-card-content>
          </ion-card>
        </ion-list>

        <div v-if="items.length === 0" class="empty-state">
          <p>No tienes materias asignadas.</p>
        </div>
      </div>

      <div v-else-if="isAdmin">
        <ion-list class="custom-list">
          <ion-item button detail @click="router.push('/docentes')">
            <ion-icon :icon="school" slot="start" color="primary"></ion-icon>
            <ion-label>Directorio de Docentes</ion-label>
          </ion-item>
          <ion-item button detail @click="router.push('/alumnos')">
            <ion-icon :icon="people" slot="start" color="secondary"></ion-icon>
            <ion-label>Directorio de Alumnos</ion-label>
          </ion-item>
          <ion-item button detail @click="router.push('/grados')">
            <ion-icon :icon="layers" slot="start" color="warning"></ion-icon>
            <ion-label>Grados y Cursos</ion-label>
          </ion-item>
        </ion-list>
      </div>

      <div v-else>
        <div class="empty-state">
          <ion-icon :icon="statsChart" size="large"></ion-icon>
          <h3>Próximamente</h3>
          <p>Aquí verás tu boleta de calificaciones.</p>
        </div>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { onIonViewWillEnter } from '@ionic/vue';
import { 
  school, people, layers, book, chevronForward, statsChart 
} from 'ionicons/icons';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, 
  IonList, IonItem, IonLabel, IonIcon, IonSpinner, IonCard, IonCardContent, IonListHeader
} from '@ionic/vue';

const authStore = useAuthStore();
const router = useRouter();
const items = ref<any[]>([]);
const loading = ref(false);

const isAdmin = computed(() => authStore.user?.role_id === 1);
const isDocente = computed(() => authStore.user?.role_id === 2);

const pageTitle = computed(() => {
  if (isAdmin.value) return 'Gestión Rápida';
  if (isDocente.value) return 'Mis Cursos';
  return 'Mis Calificaciones';
});

onIonViewWillEnter(() => {
  if (isDocente.value) {
    loadCargaDocente();
  }
});

const loadCargaDocente = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/cargas?user_id=${authStore.user.id}`);
    items.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.bg-light { --background: var(--ion-background-color); }
.custom-list { background: transparent; padding-top: 10px; }
.course-card {
  margin-bottom: 10px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  background: var(--ion-card-background, white);
}
.card-flex { display: flex; align-items: center; }
.icon-box {
  width: 50px; height: 50px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; margin-right: 15px;
}
.bg-blue { background: var(--ion-color-primary); }
.info-box { flex: 1; }
.info-box h2 { margin: 0; font-size: 1rem; font-weight: 700; color: var(--ion-text-color); }
.grade-text { margin: 2px 0 0 0; font-size: 0.85rem; color: var(--ion-color-medium); }

.empty-state { text-align: center; margin-top: 50px; color: var(--ion-color-medium); }
.center-content { display: flex; justify-content: center; margin-top: 20px; }
</style>