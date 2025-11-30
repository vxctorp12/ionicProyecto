<template>
  <ion-header>
    <ion-toolbar>
      <ion-buttons slot="start">
        <ion-button color="medium" @click="cancel">Cancelar</ion-button>
      </ion-buttons>
      <ion-title>Nueva Matrícula</ion-title>
      <ion-buttons slot="end">
        <ion-button :strong="true" color="primary" @click="confirm">Guardar</ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>

  <ion-content class="ion-padding modal-bg">
    <div class="form-container">
      
      <div class="input-group">
        <label>Seleccionar Alumno</label>
        <div class="input-wrapper">
          <ion-select 
            v-model="form.user_id" 
            interface="action-sheet" 
            :interface-options="customActionSheetOptions"
            placeholder="Busca un alumno" 
            class="custom-select"
          >
            <ion-select-option v-for="alumno in alumnos" :key="alumno.id" :value="alumno.id">
              {{ alumno.name }} ({{ alumno.email }})
            </ion-select-option>
          </ion-select>
        </div>
      </div>

      <div class="input-group">
        <label>Asignar a Grado</label>
        <div class="input-wrapper">
          <ion-select 
            v-model="form.grado_id" 
            interface="action-sheet" 
            :interface-options="customActionSheetOptions"
            placeholder="Selecciona el grado" 
            class="custom-select"
          >
            <ion-select-option v-for="grado in grados" :key="grado.id" :value="grado.id">
              {{ grado.nombre }}
            </ion-select-option>
          </ion-select>
        </div>
      </div>

    </div>
  </ion-content>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { modalController } from '@ionic/vue';
import { 
  IonHeader, IonToolbar, IonButtons, IonButton, IonTitle, IonContent, 
  IonSelect, IonSelectOption 
} from '@ionic/vue';

const form = ref({
  user_id: null,
  grado_id: null
});

const alumnos = ref<any[]>([]);
const grados = ref<any[]>([]);

const customActionSheetOptions = {
  cssClass: 'custom-action-sheet',
};

onMounted(async () => {
  try {
  
    const [resAlumnos, resGrados] = await Promise.all([
      axios.get('/users?role_id=3'),
      axios.get('/grados')
    ]);
    
    alumnos.value = resAlumnos.data;
    grados.value = resGrados.data;
  } catch (error) {
    console.error('Error cargando listas');
  }
});

const cancel = () => modalController.dismiss(null, 'cancel');

const confirm = () => {
  if (!form.value.user_id || !form.value.grado_id) {
    alert('Debes seleccionar alumno y grado');
    return;
  }
  modalController.dismiss(form.value, 'confirm');
};
</script>

<style scoped>
.input-group { margin-bottom: 20px; }
.input-group label { 
  display: block; 
  font-size: 13px; 
  font-weight: 600; 
  color: var(--ion-text-color, #333); 
  margin-bottom: 8px; 
}
.input-wrapper { 
  border: 1px solid #444; 
  border-radius: 8px; 
  background: var(--ion-item-background, transparent); 
  padding: 0 12px; 
  transition: all 0.3s; 
}
.input-wrapper:focus-within { 
  border-color: var(--ion-color-primary); 
  border-width: 2px;
  box-shadow: 0 0 0 6px rgba(var(--ion-color-primary-rgb), 0.3); 
  background-color: rgba(var(--ion-color-primary-rgb), 0.05); 
}
.custom-select { 
  --placeholder-color: #666666; 
  --color: var(--ion-text-color); 
  color: var(--ion-text-color);
  height: 45px; 
  font-size: 14px; 
  width: 100%; 
}
</style>