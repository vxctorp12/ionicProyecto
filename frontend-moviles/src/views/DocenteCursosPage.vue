<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-button @click="router.push('/tabs/tab1')">
            <ion-icon slot="icon-only" :icon="arrowBack"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Mis Cursos</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <ion-list v-else class="custom-list">
        <ion-card v-for="carga in cursos" :key="carga.id" class="course-card" button @click="irACalificar(carga)">
          <ion-card-content>
            <div class="card-flex">
              <div class="icon-box bg-blue">
                <ion-icon :icon="easel" color="light"></ion-icon>
              </div>
              
              <div class="info-box">
                <h2>{{ carga.materia?.nombre }}</h2>
                <p class="grade-text">
                  <ion-icon :icon="layers" style="vertical-align: middle; margin-right: 4px;"></ion-icon>
                  {{ carga.materia?.grado?.nombre }}
                </p>
              </div>

              <ion-icon :icon="chevronForward" color="medium"></ion-icon>
            </div>
          </ion-card-content>
        </ion-card>
      </ion-list>

      <div v-if="!loading && cursos.length === 0" class="center-content empty-text">
        <p>No tienes materias asignadas.</p>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { onIonViewWillEnter } from '@ionic/vue';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonButton,
  IonList, IonCard, IonCardContent, IonIcon, IonSpinner 
} from '@ionic/vue';
import { arrowBack, easel, layers, chevronForward } from 'ionicons/icons';

const router = useRouter();
const authStore = useAuthStore();
const cursos = ref<any[]>([]);
const loading = ref(true);

onIonViewWillEnter(() => loadCursos());

const loadCursos = async () => {
  loading.value = true;
  try {
  
    const response = await axios.get(`/cargas?user_id=${authStore.user.id}`);
    cursos.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

// ...
const irACalificar = (carga: any) => {
 
  const materiaId = carga.materia_id;
  const gradoId = carga.materia.grado_id; 
  const nombre = carga.materia.nombre;

  
  router.push(`/notas/${materiaId}/${gradoId}/${nombre}`);
};
// ...
</script>

<style scoped>
.bg-light { --background: #F4F6F8; }
.custom-list { background: transparent; padding-top: 10px; }
.course-card { margin-bottom: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); background: white; }
.card-flex { display: flex; align-items: center; }
.icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 15px; }
.bg-blue { background: var(--ion-color-primary); }
.info-box { flex: 1; }
.info-box h2 { margin: 0 0 5px 0; font-size: 1.1rem; font-weight: 700; color: #333; }
.grade-text { margin: 0; font-size: 0.9rem; color: #666; display: flex; align-items: center; }
.center-content { display: flex; flex-direction: column; justify-content: center; align-items: center; height: 80%; }
.empty-text { color: #888; }
</style>