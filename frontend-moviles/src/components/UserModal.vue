<template>
  <ion-header>
    <ion-toolbar>
      <ion-buttons slot="start">
        <ion-button color="medium" @click="cancel">Cancelar</ion-button>
      </ion-buttons>
      <ion-title>{{ title }}</ion-title>
      <ion-buttons slot="end">
        <ion-button :strong="true" color="primary" @click="confirm">Guardar</ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>

  <ion-content class="ion-padding modal-bg">
    <div class="form-container">
      
      <div class="input-group">
        <label>Nombre Completo</label>
        <div class="input-wrapper">
          <ion-input v-model="form.name" placeholder="Ej. Juan Pérez" class="custom-input"></ion-input>
        </div>
      </div>

      <div class="input-group">
        <label>Correo Electrónico</label>
        <div class="input-wrapper">
          <ion-input type="email" v-model="form.email" placeholder="profe@escuela.edu" class="custom-input"></ion-input>
        </div>
      </div>

      <div class="input-group" v-if="!fixedRole">
        <label>Rol Asignado</label>
        <div class="input-wrapper">
          <ion-select v-model="form.role_id" interface="action-sheet" placeholder="Selecciona uno" class="custom-select">
            <ion-select-option :value="1">Administrador</ion-select-option>
            <ion-select-option :value="2">Docente</ion-select-option>
            <ion-select-option :value="3">Alumno</ion-select-option>
          </ion-select>
        </div>
      </div>

      <div class="input-group">
        <label>Contraseña {{ user ? '(Opcional)' : '' }}</label>
        <div class="input-wrapper">
          <ion-input type="password" v-model="form.password" placeholder="******" class="custom-input"></ion-input>
        </div>
        <p v-if="user" class="helper-text">Deja en blanco para mantener la actual.</p>
      </div>

    </div>
  </ion-content>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { modalController } from '@ionic/vue';
import { 
  IonHeader, IonToolbar, IonButtons, IonButton, IonTitle, IonContent, 
  IonInput, IonSelect, IonSelectOption 
} from '@ionic/vue';


const props = defineProps<{ 
  user?: any; 
  fixedRole?: number; 
}>();

const form = ref({
  name: '',
  email: '',
  password: '',
  role_id: null as number | null
});

const title = computed(() => {
  if (props.user) return 'Editar Usuario';

  if (props.fixedRole === 2) return 'Nuevo Docente';
  if (props.fixedRole === 3) return 'Nuevo Alumno';
  return 'Nuevo Usuario';
});

onMounted(() => {
  if (props.user) {
 
    form.value.name = props.user.name;
    form.value.email = props.user.email;
    form.value.role_id = props.user.role_id;
  } else if (props.fixedRole) {
   
    form.value.role_id = props.fixedRole;
  }
});

const cancel = () => modalController.dismiss(null, 'cancel');

const confirm = () => {
  if (!form.value.name || !form.value.email || !form.value.role_id) {
    alert('Por favor completa los campos obligatorios');
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
  box-shadow: 0 0 0 4px rgba(var(--ion-color-primary-rgb), 0.15); 
  background: rgba(var(--ion-color-primary-rgb), 0.05);
}
.custom-input, .custom-select { 
  --padding-start: 0; 
  --background: transparent; 
  --placeholder-color: #666666; 
  --color: var(--ion-text-color); 
  color: var(--ion-text-color);
  height: 45px; 
  font-size: 14px; 
  --highlight-height: 0; 
  width: 100%;
}
.helper-text { font-size: 12px; color: #666; margin-top: 5px; }
</style>