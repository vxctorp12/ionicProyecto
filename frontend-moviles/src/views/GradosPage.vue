<template>
  <ion-page>
    <ion-header class="ion-no-border header-safe-area">
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-button @click="router.push('/tabs/tab1')">
            <ion-icon slot="icon-only" :icon="arrowBack"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Gestión de Grados</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <ion-list v-else class="custom-list">
        <ion-item-sliding v-for="grado in grados" :key="grado.id" class="grado-card">
          <ion-item lines="none" button @click="openModal(grado)">
            <div slot="start" class="icon-box orange-light">
              <ion-icon :icon="layers" color="warning"></ion-icon>
            </div>
            <ion-label>
              <h2>{{ grado.nombre }}</h2>
            </ion-label>
            <ion-icon :icon="createOutline" slot="end" color="medium" size="small"></ion-icon>
          </ion-item>

          <ion-item-options side="end">
            <ion-item-option color="danger" @click="deleteGrado(grado.id)">
              <ion-icon slot="icon-only" :icon="trashOutline"></ion-icon>
            </ion-item-option>
          </ion-item-options>
        </ion-item-sliding>
      </ion-list>

      <div v-if="!loading && grados.length === 0" class="center-content empty-text">
        <p>No hay grados aún.</p>
      </div>

      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button @click="openModal(null)" color="warning">
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
import { add, trashOutline, arrowBack, layers, createOutline } from 'ionicons/icons';
import GradoModal from '@/components/GradoModal.vue';

const router = useRouter();
const grados = ref<any[]>([]);
const loading = ref(true);

onIonViewWillEnter(() => loadGrados());

const loadGrados = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/grados');
    grados.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const openModal = async (gradoEdit: any = null) => {
  const modal = await modalController.create({
    component: GradoModal,
    componentProps: { grado: gradoEdit }
  });
  modal.present();

  const { data, role } = await modal.onWillDismiss();
  if (role === 'confirm') {
    try {
      if (gradoEdit) {
        await axios.put(`/grados/${gradoEdit.id}`, data);
        showToast('Grado actualizado');
      } else {
        await axios.post('/grados', data);
        showToast('Grado creado');
      }
      loadGrados();
    } catch (e) { showToast('Error al guardar', 'danger'); }
  }
};

const deleteGrado = async (id: number) => {
  const alert = await alertController.create({
    header: 'Eliminar Grado',
    message: 'Si eliminas este grado, podrías afectar materias asignadas.',
    buttons: [
      { text: 'Cancelar', role: 'cancel' },
      { 
        text: 'Eliminar', role: 'destructive',
        handler: async () => {
          try { await axios.delete(`/grados/${id}`); loadGrados(); } 
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
.grado-card { margin-bottom: 10px; border-radius: 12px; background: var(--ion-card-background, white); box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
.icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; }
.orange-light { background: rgba(var(--ion-color-warning-rgb), 0.15); }
.center-content { display: flex; flex-direction: column; justify-content: center; align-items: center; height: 80%; }
.empty-text { color: var(--ion-color-medium); }
</style>