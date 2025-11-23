<template>
  <ion-page>
    <ion-header class="ion-no-border header-safe-area">
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-button @click="router.push('/tabs/tab1')">
            <ion-icon slot="icon-only" :icon="arrowBack"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Carga Académica</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <ion-list v-else class="custom-list">
        <ion-item-sliding v-for="item in cargas" :key="item.id" class="user-card">
          
          <ion-item lines="none">
            <div slot="start" class="icon-box bg-teal">
              <ion-icon :icon="briefcase" color="secondary"></ion-icon>
            </div>
            
            <ion-label>
              <h2>{{ item.materia?.nombre }}</h2>
              <p>Docente: <strong>{{ item.docente?.name }}</strong></p>
              <p style="font-size: 0.8em; color: var(--ion-color-medium);">{{ item.materia?.grado?.nombre }}</p>
            </ion-label>
          </ion-item>

          <ion-item-options side="end">
            <ion-item-option color="danger" @click="deleteCarga(item.id)">
              <ion-icon slot="icon-only" :icon="trashOutline"></ion-icon>
            </ion-item-option>
          </ion-item-options>

        </ion-item-sliding>
      </ion-list>
      
      <div v-if="!loading && cargas.length === 0" class="center-content empty-text">
        <p>No hay asignaciones docentes registradas.</p>
      </div>

      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button @click="openModal" color="secondary">
          <ion-icon :icon="add"></ion-icon>
        </ion-fab-button>
      </ion-fab>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonButton,
  IonList, IonItem, IonLabel, IonSpinner, IonFab, IonFabButton, IonIcon,
  IonItemSliding, IonItemOptions, IonItemOption, onIonViewWillEnter, 
  modalController, alertController, toastController
} from '@ionic/vue';
import { add, trashOutline, arrowBack, briefcase } from 'ionicons/icons';
import CargaModal from '@/components/CargaModal.vue';

const router = useRouter();
const cargas = ref<any[]>([]);
const loading = ref(true);

onIonViewWillEnter(() => loadCargas());

const loadCargas = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/cargas');
    cargas.value = response.data;
  } catch (error) { console.error(error); } 
  finally { loading.value = false; }
};

const openModal = async () => {
  const modal = await modalController.create({
    component: CargaModal
  });
  modal.present();

  const { data, role } = await modal.onWillDismiss();
  if (role === 'confirm') {
    try {
      await axios.post('/cargas', data);
      showToast('Materia asignada correctamente');
      loadCargas();
    } catch (e: any) { 
      const msg = e.response?.data?.message || 'Error al asignar';
      showToast(msg, 'danger'); 
    }
  }
};

const deleteCarga = async (id: number) => {
  const alert = await alertController.create({
    header: 'Eliminar Asignación',
    message: 'El docente dejará de tener acceso a esta materia.',
    buttons: [
      { text: 'Cancelar', role: 'cancel' },
      { 
        text: 'Eliminar', role: 'destructive',
        handler: async () => {
          try { await axios.delete(`/cargas/${id}`); loadCargas(); } 
          catch (e) { showToast('Error al eliminar', 'danger'); }
        }
      }
    ]
  });
  await alert.present();
};

const showToast = async (msg: string, color = 'success') => {
  const t = await toastController.create({ message: msg, duration: 2000, color, position: 'bottom' });
  t.present();
};
</script>

<style scoped>
.bg-light { --background: var(--ion-background-color); }
.custom-list { background: transparent; padding-top: 10px; }
.user-card { margin-bottom: 10px; border-radius: 12px; background: var(--ion-card-background, white); box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
.icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; }
.bg-teal { background: rgba(var(--ion-color-secondary-rgb), 0.15); }
.center-content { display: flex; justify-content: center; align-items: center; height: 80%; }
.empty-text { color: var(--ion-color-medium); }
</style>