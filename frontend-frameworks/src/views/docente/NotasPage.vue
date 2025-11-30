<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar color="primary" dark>
            <v-btn icon @click="goBack">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>{{ nombreMateria }}</v-toolbar-title>
            
            <template v-slot:extension>
              <v-tabs v-model="periodoSeleccionado" align-tabs="center">
                <v-tab value="Periodo 1">Periodo 1</v-tab>
                <v-tab value="Periodo 2">Periodo 2</v-tab>
                <v-tab value="Periodo 3">Periodo 3</v-tab>
              </v-tabs>
            </template>
          </v-toolbar>

          <v-card-text>
            <div v-if="!vistaCalificar">
              <div class="d-flex justify-space-between align-center mb-4">
                <span class="text-h6">Actividades del {{ periodoSeleccionado }}</span>
                <v-btn color="primary" prepend-icon="mdi-plus" @click="crearActividad">
                  Nueva Actividad
                </v-btn>
              </div>

              <v-list lines="two">
                <v-list-item
                  v-for="act in actividades"
                  :key="act.id"
                  :title="act.nombre"
                  subtitle="Toque para calificar"
                  @click="abrirActividad(act)"
                  link
                >
                  <template v-slot:prepend>
                    <v-avatar color="grey-lighten-2">
                      <v-icon>mdi-clipboard-list</v-icon>
                    </v-avatar>
                  </template>
                  <template v-slot:append>
                    <v-icon>mdi-chevron-right</v-icon>
                  </template>
                </v-list-item>
              </v-list>

              <div v-if="actividades.length === 0" class="text-center mt-4">
                <p class="text-medium-emphasis">No hay actividades creadas en este periodo.</p>
              </div>
            </div>

            <div v-else>
              <div class="d-flex align-center mb-4">
                <v-btn icon variant="text" @click="vistaCalificar = false" class="mr-2">
                  <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
                <span class="text-h6">Calificando: {{ actividadActual.nombre }}</span>
              </div>

              <v-table>
                <thead>
                  <tr>
                    <th>Alumno</th>
                    <th style="width: 150px">Nota (0-10)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="alumno in listaAlumnos" :key="alumno.matricula_id">
                    <td>{{ alumno.nombre }}</td>
                    <td>
                      <v-text-field
                        v-model="alumno.nota_valor"
                        type="number"
                        min="0"
                        max="10"
                        density="compact"
                        hide-details
                        variant="outlined"
                        @input="validarInput($event, alumno)"
                        @blur="guardarNota(alumno)"
                      ></v-text-field>
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="dialogActividad" max-width="400">
      <v-card>
        <v-card-title>Nueva Actividad</v-card-title>
        <v-card-text>
          <v-text-field
            v-model="nuevaActividadNombre"
            label="Nombre de la actividad"
            placeholder="Ej. Examen Parcial"
            autofocus
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="dialogActividad = false">Cancelar</v-btn>
          <v-btn color="primary" variant="text" @click="confirmarCrearActividad">Crear</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="snackbar" :color="snackbarColor" timeout="2000">
      {{ snackbarText }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const materiaId = route.params.materiaId;
const gradoId = route.params.gradoId;
const nombreMateria = route.params.nombreMateria;

const periodoSeleccionado = ref('Periodo 1');
const actividades = ref([]);
const vistaCalificar = ref(false);
const actividadActual = ref(null);
const listaAlumnos = ref([]);

const dialogActividad = ref(false);
const nuevaActividadNombre = ref('');

const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const goBack = () => {
  if (vistaCalificar.value) {
    vistaCalificar.value = false;
  } else {
    router.push('/mis-cursos');
  }
};

const cargarActividades = async () => {
  vistaCalificar.value = false;
  try {
    const res = await axios.get(`/actividades?materia_id=${materiaId}&periodo=${periodoSeleccionado.value}`);
    actividades.value = res.data;
  } catch (e) {
    console.error(e);
  }
};

// Watch for period changes to reload activities
watch(periodoSeleccionado, () => {
  cargarActividades();
});

onMounted(() => {
  cargarActividades();
});

const crearActividad = () => {
  nuevaActividadNombre.value = '';
  dialogActividad.value = true;
};

const confirmarCrearActividad = async () => {
  if (!nuevaActividadNombre.value) return;

  try {
    await axios.post('/actividades', {
      materia_id: materiaId,
      periodo: periodoSeleccionado.value,
      nombre: nuevaActividadNombre.value
    });
    dialogActividad.value = false;
    cargarActividades();
    showSnackbar('Actividad creada');
  } catch (e) {
    showSnackbar('Error al crear actividad', 'error');
  }
};

const abrirActividad = async (actividad) => {
  actividadActual.value = actividad;
  
  try {
    const [resMatriculas, resNotas] = await Promise.all([
      axios.get(`/matriculas?grado_id=${gradoId}`),
      axios.get(`/notas?actividad_id=${actividad.id}`)
    ]);

    listaAlumnos.value = resMatriculas.data.map((mat) => {
      const nota = resNotas.data.find((n) => n.matricula_id === mat.id);
      return {
        matricula_id: mat.id,
        nombre: mat.user.name,
        nota_valor: nota ? nota.valor : null
      };
    });

    vistaCalificar.value = true;
  } catch (e) {
    console.error(e);
    showSnackbar('Error al cargar alumnos', 'error');
  }
};

const validarInput = (ev, alumno) => {
  let valor = parseFloat(ev.target.value);
  if (valor > 10) alumno.nota_valor = 10;
  if (valor < 0) alumno.nota_valor = 0;
};

const guardarNota = async (alumno) => {
  if (alumno.nota_valor === null || alumno.nota_valor === '') return;

  const valor = parseFloat(alumno.nota_valor);
  if (valor < 0 || valor > 10) {
    showSnackbar('La nota debe ser entre 0 y 10', 'error');
    return;
  }

  try {
    await axios.post('/notas', {
      matricula_id: alumno.matricula_id,
      actividad_id: actividadActual.value.id,
      valor: valor 
    });
    showSnackbar('Nota guardada');
  } catch (e) { 
    console.error(e);
    showSnackbar('Error al guardar nota', 'error');
  }
};

const showSnackbar = (text, color = 'success') => {
  snackbarText.value = text;
  snackbarColor.value = color;
  snackbar.value = true;
};
</script>
