<template>
  <ion-page>
    <ion-header class="ion-no-border header-safe-area">
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-button @click="router.push('/tabs/tab1')">
            <ion-icon slot="icon-only" :icon="arrowBack"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Gestión de Materias</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <ion-list v-else class="custom-list">
        <ion-item-sliding v-for="materia in materias" :key="materia.id" class="materia-card">
          <ion-item lines="none" button @click="openModal(materia)">
            
            <div slot="start" class="icon-box purple-light">
              <ion-icon :icon="library" color="tertiary"></ion-icon>
            </div>
            
            <ion-label>
              <h2>{{ materia.nombre }}</h2>
              <p v-if="materia.grado">{{ materia.grado.nombre }}</p>
            </ion-label>
            
            <ion-icon :icon="createOutline" slot="end" color="medium" size="small"></ion-icon>
          </ion-item>

          <ion-item-options side="end">
            <ion-item-option color="danger" @click="deleteMateria(materia.id)">
              <ion-icon slot="icon-only" :icon="trashOutline"></ion-icon>
            </ion-item-option>
          </ion-item-options>
        </ion-item-sliding>
      </ion-list>
      <div v-if="!loading && materias.length === 0" class="center-content empty-text">
        <p>No hay materias aún.</p>
      </div>
      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button @click="openModal(null)" color="tertiary">
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
import { add, trashOutline, arrowBack, library, createOutline } from 'ionicons/icons';
import MateriaModal from '@/components/MateriaModal.vue';

const router = useRouter();
const materias = ref<any[]>([]);
const loading = ref(true);

onIonViewWillEnter(() => loadMaterias());

const loadMaterias = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/materias');
    materias.value = response.data;
  } catch (error) { console.error(error); } 
  finally { loading.value = false; }
};

const openModal = async (materiaEdit: any = null) => {
  const modal = await modalController.create({
    component: MateriaModal,
    componentProps: { materia: materiaEdit }
  });
  modal.present();

  const { data, role } = await modal.onWillDismiss();
  if (role === 'confirm') {
    try {
      if (materiaEdit) {
        await axios.put(`/materias/${materiaEdit.id}`, data);
        showToast('Materia actualizada');
      } else {
        await axios.post('/materias', data);
        showToast('Materia creada');
      }
      loadMaterias();
    } catch (e) { showToast('Error al guardar', 'danger'); }
  }
};

const deleteMateria = async (id: number) => {
  const alert = await alertController.create({
    header: 'Eliminar Materia',
    message: '¿Estás seguro? Se borrarán las notas asociadas.',
    buttons: [
      { text: 'Cancelar', role: 'cancel' },
      { 
        text: 'Eliminar', role: 'destructive',
        handler: async () => {
          try { await axios.delete(`/materias/${id}`); loadMaterias(); } 
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
.materia-card { margin-bottom: 10px; border-radius: 12px; background: var(--ion-card-background, white); box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
.icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; }
.purple-light { background: rgba(var(--ion-color-tertiary-rgb), 0.15); }
.center-content { display: flex; flex-direction: column; justify-content: center; align-items: center; height: 80%; }
.empty-text { color: var(--ion-color-medium); }
</style>