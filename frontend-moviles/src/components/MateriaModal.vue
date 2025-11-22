<template>
  <ion-header>
    <ion-toolbar>
      <ion-buttons slot="start">
        <ion-button color="medium" @click="cancel">Cancelar</ion-button>
      </ion-buttons>
      <ion-title>{{ materia ? 'Editar Materia' : 'Nueva Materia' }}</ion-title>
      <ion-buttons slot="end">
        <ion-button :strong="true" color="primary" @click="confirm">Guardar</ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>

  <ion-content class="ion-padding">
    
    <div class="input-group">
      <label>Nombre de la Materia</label>
      <div class="input-wrapper">
        <ion-input 
            v-model="form.nombre" 
            placeholder="Ej. Matemáticas I" 
            class="custom-input"
        ></ion-input>
      </div>
    </div>

    <div class="input-group">
      <label>Asignar a Grado</label>
      <div class="input-wrapper">
        <ion-select 
            v-model="form.grado_id" 
            interface="action-sheet" 
            placeholder="Selecciona el grado" 
            class="custom-select"
        >
          <ion-select-option v-for="grado in grados" :key="grado.id" :value="grado.id">
            {{ grado.nombre }}
          </ion-select-option>
        </ion-select>
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
  IonInput, IonSelect, IonSelectOption 
} from '@ionic/vue';

const props = defineProps<{ materia?: any; }>();

const form = ref({
  nombre: '',
  grado_id: null as number | null
});

const grados = ref<any[]>([]);


onMounted(async () => {

  try {
    const response = await axios.get('/grados');
    grados.value = response.data;
  } catch (e) { console.error('Error cargando grados'); }


  if (props.materia) {
    form.value.nombre = props.materia.nombre;
    form.value.grado_id = props.materia.grado_id;
  }
});

const cancel = () => modalController.dismiss(null, 'cancel');

const confirm = () => {
  if (!form.value.nombre || !form.value.grado_id) {
    alert('Todos los campos son obligatorios');
    return;
  }
  modalController.dismiss(form.value, 'confirm');
};
</script>

<style scoped>
.input-group { 
  margin-top: 20px; 
}

.input-group label { 
  display: block; 
  font-size: 13px; 
  font-weight: 600; 
  color: #333; 
  margin-bottom: 8px; 
}


.input-wrapper { 
  border: 1px solid #E0E0E0; 
  border-radius: 8px; 
  background: #FFFFFF; 
  padding: 0 12px;
  
 
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}


.input-wrapper:focus-within { 
  border-color: var(--ion-color-primary); 

  box-shadow: 0 0 0 4px rgba(var(--ion-color-primary-rgb), 0.15); 
}


.custom-input, .custom-select { 
  --padding-start: 0; 
  --background: transparent; 
  height: 45px; 
  --color: #1A1A1A;
  

  --highlight-height: 0; 
  --highlight-color-focused: transparent;
}
</style>