<template>
  <v-dialog v-model="dialog" max-width="500px">
    <template v-slot:activator="{ props }">
      <v-btn
        color="primary"
        variant="text"
        prepend-icon="mdi-lock-reset"
        v-bind="props"
      >
        Cambiar Contraseña
      </v-btn>
    </template>

    <v-card>
      <v-card-title>
        <span class="text-h5">Cambiar Contraseña</span>
      </v-card-title>

      <v-card-text>
        <v-form @submit.prevent="handleChangePassword" ref="form">
          <v-text-field
            v-model="currentPassword"
            label="Contraseña Actual"
            type="password"
            :rules="[v => !!v || 'Requerido']"
            required
          ></v-text-field>
          
          <v-text-field
            v-model="newPassword"
            label="Nueva Contraseña"
            type="password"
            :rules="[v => !!v || 'Requerido', v => v.length >= 6 || 'Mínimo 6 caracteres']"
            required
          ></v-text-field>

          <v-text-field
            v-model="confirmPassword"
            label="Confirmar Nueva Contraseña"
            type="password"
            :rules="[v => !!v || 'Requerido', v => v === newPassword || 'Las contraseñas no coinciden']"
            required
          ></v-text-field>
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="dialog = false">Cancelar</v-btn>
        <v-btn color="primary" @click="handleChangePassword" :loading="loading">Guardar</v-btn>
      </v-card-actions>
    </v-card>

    <v-snackbar v-model="snackbar" :color="snackbarColor">
      {{ snackbarText }}
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar = false">Cerrar</v-btn>
      </template>
    </v-snackbar>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const dialog = ref(false);
const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const loading = ref(false);
const form = ref(null);

const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const authStore = useAuthStore();

const handleChangePassword = async () => {
  const { valid } = await form.value.validate();
  if (!valid) return;

  loading.value = true;
  try {
    await authStore.changePassword(currentPassword.value, newPassword.value);
    showSnackbar('Contraseña actualizada correctamente');
    dialog.value = false;
    // Reset form
    currentPassword.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
  } catch (error) {
    showSnackbar('Error al cambiar contraseña. Verifica tu contraseña actual.', 'error');
  } finally {
    loading.value = false;
  }
};

const showSnackbar = (text, color = 'success') => {
  snackbarText.value = text;
  snackbarColor.value = color;
  snackbar.value = true;
};
</script>
