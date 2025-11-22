<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon slot="icon-only" :icon="arrowBack"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Gestión de Alumnos</ion-title> </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <ion-list v-else class="custom-list">
        <ion-item-sliding v-for="alumno in alumnos" :key="alumno.id" class="user-card">
          
          <ion-item lines="none" :detail="true" button @click="editAlumno(alumno)">
            <div slot="start" class="avatar-circle bg-purple">
              {{ alumno.name.charAt(0).toUpperCase() }}
            </div>
            <ion-label>
              <h2>{{ alumno.name }}</h2>
              <p>{{ alumno.email }}</p>
            </ion-label>
            <ion-badge slot="end" color="tertiary">Alumno</ion-badge>
          </ion-item>

          <ion-item-options side="end">
            <ion-item-option color="danger" @click="deleteAlumno(alumno.id)">
              <ion-icon slot="icon-only" :icon="trashOutline"></ion-icon>
            </ion-item-option>
          </ion-item-options>

        </ion-item-sliding>
      </ion-list>
      <div v-if="!loading && alumnos.length === 0" class="center-content empty-text">
        <p>No hay alumnos aún.</p>
      </div>
      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button @click="createAlumno" color="tertiary">
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
const alumnos = ref<any[]>([]);
const loading = ref(true);

const goBack = () => router.push('/tabs/tab1');

onIonViewWillEnter(() => loadAlumnos());

const loadAlumnos = async () => {
  loading.value = true;
  try {
    
    const response = await axios.get('/users?role_id=3'); 
    alumnos.value = response.data;
  } catch (error) { console.error(error); } 
  finally { loading.value = false; }
};


const openModal = async (userToEdit: any = null) => {
  const modal = await modalController.create({
    component: UserModal,
    componentProps: { 
      user: userToEdit,
      fixedRole: 3 
    }
  });

  modal.present();
  const { data, role } = await modal.onWillDismiss();

  if (role === 'confirm') {
    try {
      if (userToEdit) {
        await axios.put(`/users/${userToEdit.id}`, data);
      } else {
        await axios.post('/users', data);
      }
      loadAlumnos();
    } catch (e) {}
  }
};
const createAlumno = () => openModal(null);
const editAlumno = (user: any) => openModal(user);
const deleteAlumno = async (id: number) => {
    await axios.delete(`/users/${id}`);
    loadAlumnos();
};
</script>

<style scoped>

.bg-light { --background: #F4F6F8; }
.custom-list { background: transparent; padding-top: 10px; }
.user-card { margin-bottom: 10px; border-radius: 12px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
.avatar-circle { width: 40px; height: 40px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; font-size: 16px; }
.bg-purple { background: var(--ion-color-tertiary); }
.center-content { display: flex; justify-content: center; align-items: center; height: 80%; }
</style>