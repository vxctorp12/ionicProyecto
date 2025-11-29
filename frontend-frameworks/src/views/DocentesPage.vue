<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card>
          <v-toolbar color="info" dark>
            <v-btn icon @click="router.push('/dashboard')">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Gestión de Docentes</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="openDialog()">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-toolbar>

          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="docentes"
              :loading="loading"
              class="elevation-1"
            >
              <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-4" @click="openDialog(item)">
                  mdi-pencil
                </v-icon>
                <v-icon small color="error" @click="confirmDelete(item)">
                  mdi-delete
                </v-icon>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog for Create/Edit -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ editedItem.id ? 'Editar Docente' : 'Nuevo Docente' }}</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="editedItem.name"
                  label="Nombre Completo"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="editedItem.email"
                  label="Correo Electrónico"
                  type="email"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="editedItem.password"
                  label="Contraseña"
                  type="password"
                  :hint="editedItem.id ? 'Dejar en blanco para mantener actual' : 'Requerido para nuevos usuarios'"
                  persistent-hint
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDialog">Cancelar</v-btn>
          <v-btn color="blue darken-1" text @click="save">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog for Delete Confirmation -->
    <v-dialog v-model="dialogDelete" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">¿Estás seguro de eliminar este docente?</v-card-title>
        <v-card-text>Se perderá su acceso al sistema.</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDelete">Cancelar</v-btn>
          <v-btn color="error" text @click="deleteItemConfirm">Eliminar</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="snackbar" :color="snackbarColor">
      {{ snackbarText }}
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar = false">Cerrar</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const docentes = ref([]);
const loading = ref(true);
const dialog = ref(false);
const dialogDelete = ref(false);
const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Nombre', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Acciones', key: 'actions', sortable: false },
];
const editedItem = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  role_id: 2 // Docente Role ID
});
const defaultItem = {
  id: null,
  name: '',
  email: '',
  password: '',
  role_id: 2
};
const itemToDelete = ref(null);

const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const loadDocentes = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/users?role_id=2');
    docentes.value = response.data;
  } catch (error) {
    console.error(error);
    showSnackbar('Error al cargar docentes', 'error');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadDocentes();
});

const openDialog = (item) => {
  if (item) {
    editedItem.value = { ...item, password: '' }; // Don't show password
  } else {
    editedItem.value = { ...defaultItem };
  }
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  editedItem.value = { ...defaultItem };
};

const save = async () => {
  if (!editedItem.value.name || !editedItem.value.email) {
    showSnackbar('Nombre y Email son obligatorios', 'warning');
    return;
  }

  try {
    if (editedItem.value.id) {
      // If password is empty, remove it so it's not updated
      const data = { ...editedItem.value };
      if (!data.password) delete data.password;
      
      await axios.put(`/users/${editedItem.value.id}`, data);
      showSnackbar('Docente actualizado');
    } else {
      await axios.post('/users', editedItem.value);
      showSnackbar('Docente creado');
    }
    loadDocentes();
    closeDialog();
  } catch (error) {
    showSnackbar('Error al guardar', 'error');
  }
};

const confirmDelete = (item) => {
  itemToDelete.value = item;
  dialogDelete.value = true;
};

const closeDelete = () => {
  dialogDelete.value = false;
  itemToDelete.value = null;
};

const deleteItemConfirm = async () => {
  if (itemToDelete.value) {
    try {
      await axios.delete(`/users/${itemToDelete.value.id}`);
      showSnackbar('Docente eliminado');
      loadDocentes();
    } catch (error) {
      showSnackbar('Error al eliminar', 'error');
    }
  }
  closeDelete();
};

const showSnackbar = (text, color = 'success') => {
  snackbarText.value = text;
  snackbarColor.value = color;
  snackbar.value = true;
};
</script>
