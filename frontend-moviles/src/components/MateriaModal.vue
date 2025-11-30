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

const customActionSheetOptions = {
  cssClass: 'custom-action-sheet',
};


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
  box-shadow: 0 0 0 4px rgba(var(--ion-color-primary-rgb), 0.15);
  background: rgba(var(--ion-color-primary-rgb), 0.05);
}

.custom-input, .custom-select {
  --padding-start: 0;
  --background: transparent;
  height: 45px;
  --color: var(--ion-text-color);
  color: var(--ion-text-color);

  --placeholder-color: #666666;
  --placeholder-opacity: 1;

  --highlight-height: 0;
  --highlight-color-focused: transparent;
  width: 100%;
}
</style>