<template>
  <ion-header>
    <ion-toolbar>
      <ion-buttons slot="start">
        <ion-button color="medium" @click="cancel">Cancelar</ion-button>
      </ion-buttons>
      <ion-title>Asignar Materia</ion-title>
      <ion-buttons slot="end">
        <ion-button :strong="true" color="primary" @click="confirm">Guardar</ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>

  <ion-content class="ion-padding modal-bg">
    <div class="form-container">
      
      <div class="input-group">
        <label>Seleccionar Docente</label>
        <div class="input-wrapper">
          <ion-select 
            v-model="form.user_id" 
            interface="action-sheet" 
            placeholder="Busca un docente" 
            class="custom-select"
          >
            <ion-select-option v-for="docente in docentes" :key="docente.id" :value="docente.id">
              {{ docente.name }}
            </ion-select-option>
          </ion-select>
        </div>
      </div>

      <div class="input-group">
        <label>Materia a Impartir</label>
        <div class="input-wrapper">
          <ion-select 
            v-model="form.materia_id" 
            interface="action-sheet" 
            placeholder="Selecciona la materia" 
            class="custom-select"
          >
            <ion-select-option v-for="materia in materias" :key="materia.id" :value="materia.id">
              {{ materia.nombre }} - {{ materia.grado?.nombre }}
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
  materia_id: null
});

const docentes = ref<any[]>([]);
const materias = ref<any[]>([]);

onMounted(async () => {
  try {

    const [resDocentes, resMaterias] = await Promise.all([
      axios.get('/users?role_id=2'),
      axios.get('/materias')
    ]);
    
    docentes.value = resDocentes.data;
    materias.value = resMaterias.data;
  } catch (error) {
    console.error('Error cargando listas');
  }
});

const cancel = () => modalController.dismiss(null, 'cancel');

const confirm = () => {
  if (!form.value.user_id || !form.value.materia_id) {
    alert('Debes seleccionar docente y materia');
    return;
  }
  modalController.dismiss(form.value, 'confirm');
};
</script>

<style scoped>
.input-group { margin-bottom: 20px; }
.input-group label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; }
.input-wrapper { border: 1px solid #E0E0E0; border-radius: 8px; background: #FFFFFF; padding: 0 12px; transition: all 0.3s; }
.input-wrapper:focus-within { border-color: var(--ion-color-primary); box-shadow: 0 0 0 4px rgba(var(--ion-color-primary-rgb), 0.15); }
.custom-select { --placeholder-color: #A0A0A0; --color: #1A1A1A !important; height: 45px; font-size: 14px; width: 100%; }
</style>