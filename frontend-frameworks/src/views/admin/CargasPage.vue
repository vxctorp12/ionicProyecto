<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card>
          <v-toolbar color="teal" dark>
            <v-btn icon @click="router.push('/dashboard')">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Carga Académica</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="openDialog()">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-toolbar>

          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="cargas"
              :loading="loading"
              class="elevation-1"
            >
              <template v-slot:item.materia="{ item }">
                {{ item.materia ? item.materia.nombre : 'Desconocida' }}
              </template>
              <template v-slot:item.docente="{ item }">
                {{ item.docente ? item.docente.name : 'Sin asignar' }}
              </template>
              <template v-slot:item.grado="{ item }">
                {{ item.materia && item.materia.grado ? item.materia.grado.nombre : '-' }}
              </template>
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

    <!-- Dialog for Create -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Asignar Materia</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-select
                  v-model="newItem.user_id"
                  :items="docentes"
                  item-title="name"
                  item-value="id"
                  label="Seleccionar Docente"
                  required
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="newItem.materia_id"
                  :items="materias"
                  item-title="nombre_completo"
                  item-value="id"
                  label="Materia a Impartir"
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
        <v-card-title class="text-h5">¿Eliminar Asignación?</v-card-title>
        <v-card-text>El docente dejará de tener acceso a esta materia.</v-card-text>
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
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const cargas = ref([]);
const docentes = ref([]);
const materias = ref([]);
const loading = ref(true);
const dialog = ref(false);
const dialogDelete = ref(false);
const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Materia', key: 'materia' },
  { title: 'Docente', key: 'docente' },
  { title: 'Grado', key: 'grado' },
  { title: 'Acciones', key: 'actions', sortable: false },
];
const newItem = ref({
  user_id: null,
  materia_id: null,
});
const defaultItem = {
  user_id: null,
  materia_id: null,
};
const itemToDelete = ref(null);

const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const loadCargas = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/cargas');
    cargas.value = response.data;
  } catch (error) {
    console.error(error);
    showSnackbar('Error al cargar asignaciones', 'error');
  } finally {
    loading.value = false;
  }
};

const loadLists = async () => {
  try {
    const [resDocentes, resMaterias] = await Promise.all([
      axios.get('/users?role_id=2'),
      axios.get('/materias')
    ]);
    docentes.value = resDocentes.data;
    
    // Format materias for display in select
    materias.value = resMaterias.data.map(m => ({
      ...m,
      nombre_completo: `${m.nombre} - ${m.grado ? m.grado.nombre : 'Sin grado'}`
    }));
  } catch (error) {
    console.error('Error cargando listas', error);
  }
};

onMounted(() => {
  loadCargas();
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
  if (!newItem.value.user_id || !newItem.value.materia_id) {
    showSnackbar('Debes seleccionar docente y materia', 'warning');
    return;
  }

  try {
    await axios.post('/cargas', newItem.value);
    showSnackbar('Materia asignada correctamente');
    loadCargas();
    closeDialog();
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al asignar';
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
      await axios.delete(`/cargas/${itemToDelete.value.id}`);
      showSnackbar('Asignación eliminada');
      loadCargas();
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
