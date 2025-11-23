<template>
  <ion-page>
    <ion-header class="ion-no-border header-safe-area">
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon slot="icon-only" :icon="arrowBack"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Gestión de Docentes</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <ion-list v-else class="custom-list">
        <ion-item-sliding v-for="docente in docentes" :key="docente.id" class="user-card">
          
          <ion-item lines="none" :detail="true" button @click="editDocente(docente)">
            <div slot="start" class="avatar-circle bg-blue">
              {{ docente.name.charAt(0).toUpperCase() }}
            </div>
            <ion-label>
              <h2>{{ docente.name }}</h2>
              <p>{{ docente.email }}</p>
            </ion-label>
            <ion-badge slot="end" color="primary">Docente</ion-badge>
          </ion-item>

          <ion-item-options side="end">
            <ion-item-option color="danger" @click="deleteDocente(docente.id)">
              <ion-icon slot="icon-only" :icon="trashOutline"></ion-icon>
            </ion-item-option>
          </ion-item-options>

        </ion-item-sliding>
      </ion-list>

      <div v-if="!loading && docentes.length === 0" class="center-content empty-text">
        <p>No hay docentes registrados.</p>
      </div>

      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button @click="createDocente" color="primary">
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
  IonList, IonItem, IonLabel, IonBadge, IonSpinner, IonFab, IonFabButton, IonIcon,
  IonItemSliding, IonItemOptions, IonItemOption, onIonViewWillEnter, 
  modalController, alertController, toastController
} from '@ionic/vue';
import { add, trashOutline, arrowBack } from 'ionicons/icons';
import UserModal from '@/components/UserModal.vue';

const router = useRouter();
const docentes = ref<any[]>([]);
const loading = ref(true);


const goBack = () => router.push('/tabs/tab1');

onIonViewWillEnter(() => loadDocentes());

const loadDocentes = async () => {
  loading.value = true;
  try {
    
    const response = await axios.get('/users?role_id=2');
    docentes.value = response.data;
  } catch (error) { console.error(error); } 
  finally { loading.value = false; }
};


const openModal = async (userToEdit: any = null) => {
  const modal = await modalController.create({
    component: UserModal,
    componentProps: { 
      user: userToEdit,
      fixedRole: 2 
    }
  });

  modal.present();
  const { data, role } = await modal.onWillDismiss();

  if (role === 'confirm') {
    try {
      if (userToEdit) {
        await axios.put(`/users/${userToEdit.id}`, data);
        showToast('Docente actualizado');
      } else {
        await axios.post('/users', data);
        showToast('Docente creado');
      }
      loadDocentes();
    } catch (e) { showToast('Error al guardar', 'danger'); }
  }
};

const createDocente = () => openModal(null);
const editDocente = (user: any) => openModal(user);

const deleteDocente = async (id: number) => {
  const alert = await alertController.create({
    header: 'Eliminar Docente',
    message: '¿Estás seguro? Se perderá su acceso al sistema.',
    buttons: [
      { text: 'Cancelar', role: 'cancel' },
      { 
        text: 'Eliminar', role: 'destructive',
        handler: async () => {
          try { await axios.delete(`/users/${id}`); loadDocentes(); } 
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
.avatar-circle { width: 40px; height: 40px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; font-size: 16px; }
.bg-blue { background: var(--ion-color-primary); }
.center-content { display: flex; justify-content: center; align-items: center; height: 80%; }
.empty-text { color: var(--ion-color-medium); }
</style>