<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card>
          <v-toolbar color="warning" dark>
            <v-btn icon @click="router.push('/dashboard')">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Gestión de Inscripciones</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="openDialog()">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-toolbar>

          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="matriculas"
              :loading="loading"
              class="elevation-1"
            >
              <template v-slot:item.alumno="{ item }">
                {{ item.user ? item.user.name : 'Desconocido' }}
              </template>
              <template v-slot:item.grado="{ item }">
                {{ item.grado ? item.grado.nombre : 'Sin grado' }}
              </template>
              <template v-slot:item.anio="{ item }">
                {{ item.anio }}
              </template>
              <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-4" @click="confirmDelete(item)">
                  mdi-delete
                </v-icon>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog for Create -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Nueva Matrícula</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-select
                  v-model="newItem.user_id"
                  :items="alumnos"
                  item-title="name"
                  item-value="id"
                  label="Seleccionar Alumno"
                  required
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="newItem.grado_id"
                  :items="grados"
                  item-title="nombre"
                  item-value="id"
                  label="Asignar a Grado"
                  required
                ></v-select>
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
        <v-card-title class="text-h5">¿Cancelar Matrícula?</v-card-title>
        <v-card-text>¿Estás seguro de retirar a este alumno del grado?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDelete">Cancelar</v-btn>
          <v-btn color="error" text @click="deleteItemConfirm">Retirar</v-btn>
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
const matriculas = ref([]);
const alumnos = ref([]);
const grados = ref([]);
const loading = ref(true);
const dialog = ref(false);
const dialogDelete = ref(false);
const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Alumno', key: 'alumno' },
  { title: 'Grado', key: 'grado' },
  { title: 'Año', key: 'anio' },
  { title: 'Acciones', key: 'actions', sortable: false },
];
const newItem = ref({
  user_id: null,
  grado_id: null,
});
const defaultItem = {
  user_id: null,
  grado_id: null,
};
const itemToDelete = ref(null);

const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const loadMatriculas = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/matriculas');
    matriculas.value = response.data;
  } catch (error) {
    console.error(error);
    showSnackbar('Error al cargar matrículas', 'error');
  } finally {
    loading.value = false;
  }
};

const loadLists = async () => {
  try {
    const [resAlumnos, resGrados] = await Promise.all([
      axios.get('/users?role_id=3'),
      axios.get('/grados')
    ]);
    alumnos.value = resAlumnos.data;
    grados.value = resGrados.data;
  } catch (error) {
    console.error('Error cargando listas', error);
  }
};

onMounted(() => {
  loadMatriculas();
  loadLists();
});

const openDialog = () => {
  newItem.value = { ...defaultItem };
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  newItem.value = { ...defaultItem };
};

const save = async () => {
  if (!newItem.value.user_id || !newItem.value.grado_id) {
    showSnackbar('Debes seleccionar alumno y grado', 'warning');
    return;
  }

  try {
    await axios.post('/matriculas', newItem.value);
    showSnackbar('Alumno inscrito correctamente');
    loadMatriculas();
    closeDialog();
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al inscribir';
    showSnackbar(msg, 'error');
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
      await axios.delete(`/matriculas/${itemToDelete.value.id}`);
      showSnackbar('Matrícula cancelada');
      loadMatriculas();
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
