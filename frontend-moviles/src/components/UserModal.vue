<template>
  <ion-header>
    <ion-toolbar>
      <ion-buttons slot="start">
        <ion-button color="medium" @click="cancel">Cancelar</ion-button>
      </ion-buttons>
      <ion-title>{{ title }}</ion-title>
      <ion-buttons slot="end">
        <ion-button :strong="true" color="primary" @click="confirm" :disabled="!isValid">Guardar</ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>

  <ion-content class="ion-padding modal-bg">
    <div class="form-container">
      
      <div class="input-group">
        <label>Nombre Completo</label>
        <div class="input-wrapper" :class="{ 'error-border': errors.name && touched.name }">
          <ion-input 
            v-model="form.name" 
            placeholder="Ej. Juan Pérez" 
            class="custom-input"
            @ionBlur="markTouched('name')"
            @ionInput="markTouched('name')"
          ></ion-input>
        </div>
        <span v-if="errors.name && touched.name" class="error-text">{{ errors.name }}</span>
      </div>

      <div class="input-group">
        <label>Correo Electrónico</label>
        <div class="input-wrapper" :class="{ 'error-border': errors.email && touched.email }">
          <ion-input 
            type="email" 
            v-model="form.email" 
            placeholder="profe@escuela.edu" 
            class="custom-input"
            @ionBlur="handleEmailBlur"
            @ionInput="markTouched('email')"
          ></ion-input>
        </div>
        <span v-if="errors.email && touched.email" class="error-text">{{ errors.email }}</span>
      </div>

      <div class="input-group" v-if="!fixedRole">
        <label>Rol Asignado</label>
        <div class="input-wrapper" :class="{ 'error-border': errors.role_id && touched.role_id }">
          <ion-select 
            v-model="form.role_id" 
            interface="action-sheet" 
            placeholder="Selecciona uno" 
            class="custom-select"
            @ionDismiss="markTouched('role_id')"
          >
            <ion-select-option :value="1">Administrador</ion-select-option>
            <ion-select-option :value="2">Docente</ion-select-option>
            <ion-select-option :value="3">Alumno</ion-select-option>
          </ion-select>
        </div>
        <span v-if="errors.role_id && touched.role_id" class="error-text">{{ errors.role_id }}</span>
      </div>

      <div class="input-group">
        <label>Contraseña {{ user ? '(Opcional)' : '' }}</label>
        <div class="input-wrapper" :class="{ 'error-border': errors.password && touched.password }">
          <ion-input 
            type="password" 
            v-model="form.password" 
            placeholder="******" 
            class="custom-input"
            @ionBlur="markTouched('password')"
            @ionInput="markTouched('password')"
          ></ion-input>
        </div>
        <p v-if="user" class="helper-text">Deja en blanco para mantener la actual.</p>
        <span v-if="errors.password && touched.password" class="error-text">{{ errors.password }}</span>
      </div>

    </div>
  </ion-content>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
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

const errors = ref({
  name: '',
  email: '',
  password: '',
  role_id: ''
});

const touched = ref({
  name: false,
  email: false,
  password: false,
  role_id: false
});

const title = computed(() => {
  if (props.user) return 'Editar Usuario';

  if (props.fixedRole === 2) return 'Nuevo Docente';
  if (props.fixedRole === 3) return 'Nuevo Alumno';
  return 'Nuevo Usuario';
});

const isValid = computed(() => {
  return !errors.value.name && !errors.value.email && !errors.value.password && !errors.value.role_id;
});

const validate = () => {
  // Name
  if (!form.value.name.trim()) {
    errors.value.name = 'El nombre es obligatorio';
  } else {
    errors.value.name = '';
  }

  // Email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!form.value.email.trim()) {
    errors.value.email = 'El correo es obligatorio';
  } else if (!emailRegex.test(form.value.email)) {
    errors.value.email = 'Correo inválido';
  } else {
    errors.value.email = '';
  }

  // Role
  if (!props.fixedRole && !form.value.role_id) {
    errors.value.role_id = 'El rol es obligatorio';
  } else {
    errors.value.role_id = '';
  }

  if (!props.user && !form.value.password) {
    errors.value.password = 'La contraseña es obligatoria para nuevos usuarios';
  } else if (form.value.password && form.value.password.length < 6) {
    errors.value.password = 'Mínimo 6 caracteres';
  } else {
    errors.value.password = '';
  }
};

const checkEmailAvailability = async () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!form.value.email || !emailRegex.test(form.value.email)) return;

  try {
    const response = await axios.get(`/users?email=${form.value.email}`);
    const users = response.data;
    
    if (users.length > 0) {
      const existingUser = users[0];
      if (props.user && existingUser.id === props.user.id) {
        return;
      }
      errors.value.email = 'Este correo ya está registrado';
    } else {
      if (errors.value.email === 'Este correo ya está registrado') {
        errors.value.email = '';
      }
    }
  } catch (error) {
    console.error('Error checking email', error);
  }
};

const handleEmailBlur = () => {
  markTouched('email');
  checkEmailAvailability();
};

const markTouched = (field: keyof typeof touched.value) => {
  touched.value[field] = true;
};

watch(form, () => {
  validate();
}, { deep: true });

onMounted(() => {
  if (props.user) {
    form.value.name = props.user.name;
    form.value.email = props.user.email;
    form.value.role_id = props.user.role_id;
  } else if (props.fixedRole) {
    form.value.role_id = props.fixedRole;
  }
  // Initial validation (without touching) to set initial state of isValid
  validate();
});

const cancel = () => modalController.dismiss(null, 'cancel');

const confirm = async () => {
  // Touch all before submitting to show errors if any
  touched.value.name = true;
  touched.value.email = true;
  touched.value.role_id = true;
  touched.value.password = true;
  
  validate();
  await checkEmailAvailability(); // Double check before submit

  if (!isValid.value) {
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
.input-wrapper.error-border {
  border-color: var(--ion-color-danger);
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
.error-text {
  color: var(--ion-color-danger);
  font-size: 12px;
  margin-top: 5px;
  display: block;
}
</style>