<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card>
          <v-toolbar color="primary" dark>
            <v-btn icon @click="router.push('/dashboard')">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Gestión de Grados</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="openDialog()">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-toolbar>

          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="grados"
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
          <span class="text-h5">{{ editedItem.id ? 'Editar Grado' : 'Nuevo Grado' }}</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="editedItem.nombre"
                  label="Nombre del Grado"
                  required
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
        <v-card-title class="text-h5">¿Estás seguro de eliminar este grado?</v-card-title>
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
const grados = ref([]);
const loading = ref(true);
const dialog = ref(false);
const dialogDelete = ref(false);
const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Nombre', key: 'nombre' },
  { title: 'Acciones', key: 'actions', sortable: false },
];
const editedItem = ref({
  id: null,
  nombre: '',
});
const defaultItem = {
  id: null,
  nombre: '',
};
const itemToDelete = ref(null);

const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const loadGrados = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/grados');
    grados.value = response.data;
  } catch (error) {
    console.error(error);
    showSnackbar('Error al cargar grados', 'error');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadGrados();
});

const openDialog = (item) => {
  if (item) {
    editedItem.value = { ...item }; // Copy item
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
  if (!editedItem.value.nombre) {
    showSnackbar('El nombre es obligatorio', 'warning');
    return;
  }

  try {
    if (editedItem.value.id) {
      await axios.put(`/grados/${editedItem.value.id}`, editedItem.value);
      showSnackbar('Grado actualizado');
    } else {
      await axios.post('/grados', editedItem.value);
      showSnackbar('Grado creado');
    }
    loadGrados();
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
      await axios.delete(`/grados/${itemToDelete.value.id}`);
      showSnackbar('Grado eliminado');
      loadGrados();
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
