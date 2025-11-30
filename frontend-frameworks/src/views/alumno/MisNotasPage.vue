<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar color="primary" dark>
            <v-btn icon @click="router.push('/dashboard')">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Mis Calificaciones</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="abrirHistorial">
              <v-icon>mdi-history</v-icon>
            </v-btn>
          </v-toolbar>

          <v-card-text>
            <div v-if="loading" class="text-center">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </div>

            <div v-else-if="infoMatricula">
              <div class="text-center mb-4">
                <h2 class="text-h5 font-weight-bold">{{ infoMatricula.grado?.nombre }}</h2>
                <p class="text-subtitle-1 text-medium-emphasis">Ciclo Escolar {{ infoMatricula.anio }}</p>
              </div>

              <!-- Resumen de Promedios -->
              <v-row class="mb-4">
                <v-col cols="4">
                  <v-card class="text-center py-2" elevation="2">
                    <div class="text-caption text-medium-emphasis">PERIODO 1</div>
                    <div class="text-h5 font-weight-bold" :class="getColorNota(promedioVerticalP1)">
                      {{ promedioVerticalP1 }}
                    </div>
                  </v-card>
                </v-col>
                <v-col cols="4">
                  <v-card class="text-center py-2" elevation="2">
                    <div class="text-caption text-medium-emphasis">PERIODO 2</div>
                    <div class="text-h5 font-weight-bold" :class="getColorNota(promedioVerticalP2)">
                      {{ promedioVerticalP2 }}
                    </div>
                  </v-card>
                </v-col>
                <v-col cols="4">
                  <v-card class="text-center py-2" elevation="2">
                    <div class="text-caption text-medium-emphasis">PERIODO 3</div>
                    <div class="text-h5 font-weight-bold" :class="getColorNota(promedioVerticalP3)">
                      {{ promedioVerticalP3 }}
                    </div>
                  </v-card>
                </v-col>
              </v-row>

              <!-- Lista de Materias -->
              <v-row>
                <v-col v-for="materia in boleta" :key="materia.id" cols="12" md="6">
                  <v-card variant="outlined" class="mb-2">
                    <v-card-title class="text-subtitle-1 font-weight-bold">
                      {{ materia.nombre }}
                    </v-card-title>
                    <v-card-text>
                      <div class="d-flex justify-space-between text-center">
                        <div class="flex-grow-1" @click="verDetalle(materia, 'Periodo 1')" style="cursor: pointer">
                          <div class="text-caption">P1</div>
                          <div class="font-weight-bold" :class="getColorNota(materia.p1)">
                            {{ materia.p1 || '-' }}
                          </div>
                        </div>
                        <div class="flex-grow-1" @click="verDetalle(materia, 'Periodo 2')" style="cursor: pointer">
                          <div class="text-caption">P2</div>
                          <div class="font-weight-bold" :class="getColorNota(materia.p2)">
                            {{ materia.p2 || '-' }}
                          </div>
                        </div>
                        <div class="flex-grow-1" @click="verDetalle(materia, 'Periodo 3')" style="cursor: pointer">
                          <div class="text-caption">P3</div>
                          <div class="font-weight-bold" :class="getColorNota(materia.p3)">
                            {{ materia.p3 || '-' }}
                          </div>
                        </div>
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </div>

            <div v-else class="text-center mt-4">
              <p class="text-medium-emphasis">No estás matriculado en el ciclo actual.</p>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialogo Historial -->
    <v-dialog v-model="showHistorialModal" max-width="500">
      <v-card>
        <v-toolbar color="primary" dark>
          <v-toolbar-title>Historial Académico</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="showHistorialModal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>

        <v-card-text class="pt-4">
          <v-select
            v-if="aniosDisponibles.length > 0"
            v-model="anioSeleccionado"
            :items="aniosDisponibles"
            label="Seleccionar Año"
            @update:model-value="cargarDatosHistorial"
            variant="outlined"
          ></v-select>

          <div v-if="loadingHistorial" class="text-center">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
          </div>

          <div v-else-if="datosHistorial" class="text-center">
            <div class="d-flex justify-center mb-4">
              <v-avatar color="primary" size="120">
                <div class="text-white">
                  <div class="text-h3 font-weight-bold">{{ datosHistorial.promedioGeneral }}</div>
                  <div class="text-caption">Promedio Global</div>
                </div>
              </v-avatar>
            </div>

            <v-list>
              <v-list-item v-for="mat in datosHistorial.materias" :key="mat.nombre">
                <v-list-item-title class="text-left">{{ mat.nombre }}</v-list-item-title>
                <template v-slot:append>
                  <span class="font-weight-bold text-h6" :class="getColorNota(mat.final)">
                    {{ mat.final }}
                  </span>
                </template>
              </v-list-item>
            </v-list>
          </div>

          <div v-else class="text-center mt-4">
            <p class="text-medium-emphasis">Selecciona un año para ver el historial.</p>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Dialogo Detalle Notas -->
    <v-dialog v-model="showDetalleModal" max-width="400">
      <v-card>
        <v-card-title>Detalle de Notas</v-card-title>
        <v-card-subtitle>{{ detalleMateria }} - {{ detallePeriodo }}</v-card-subtitle>
        <v-card-text>
          <v-list>
            <v-list-item v-for="(nota, index) in detalleNotas" :key="index">
              <v-list-item-title>{{ nota.actividad }}</v-list-item-title>
              <template v-slot:append>
                <span class="font-weight-bold">{{ nota.valor }}</span>
              </template>
            </v-list-item>
            <v-list-item v-if="detalleNotas.length === 0">
              <v-list-item-title class="text-center text-medium-emphasis">
                No hay notas registradas
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="showDetalleModal = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const infoMatricula = ref(null);
const boleta = ref([]);
const rawNotas = ref([]);

const showHistorialModal = ref(false);
const loadingHistorial = ref(false);
const aniosDisponibles = ref([]);
const anioSeleccionado = ref(null);
const datosHistorial = ref(null);

const showDetalleModal = ref(false);
const detalleMateria = ref('');
const detallePeriodo = ref('');
const detalleNotas = ref([]);

const loadData = async () => {
  loading.value = true;
  try {
    const resMatricula = await axios.get(`/matriculas?user_id=${authStore.user.id}&activo=true`);
    
    if (resMatricula.data.length > 0) {
      infoMatricula.value = resMatricula.data[0];
      const matriculaId = infoMatricula.value.id;
      
      const resNotas = await axios.get(`/notas?matricula_id=${matriculaId}`);
      rawNotas.value = resNotas.data;

      if (infoMatricula.value.grado?.materias) {
        boleta.value = infoMatricula.value.grado.materias.map((materia) => {
          const notasDeLaMateria = rawNotas.value.filter((n) => n.actividad?.materia_id === materia.id);
          return {
            id: materia.id,
            nombre: materia.nombre,
            p1: calcularPromedioMateria(notasDeLaMateria, 'Periodo 1'),
            p2: calcularPromedioMateria(notasDeLaMateria, 'Periodo 2'),
            p3: calcularPromedioMateria(notasDeLaMateria, 'Periodo 3'),
          };
        });
      }
    }
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadData();
});

const calcularPromedioMateria = (notas, periodo) => {
  const notasDelPeriodo = notas.filter((n) => n.actividad?.periodo === periodo);
  if (notasDelPeriodo.length === 0) return null;
  const suma = notasDelPeriodo.reduce((acc, curr) => acc + parseFloat(curr.valor), 0);
  return (suma / notasDelPeriodo.length).toFixed(1);
};

const calcularPromedioVertical = (keyPeriodo) => {
  if (boleta.value.length === 0) return '-';
  
  let suma = 0;
  let count = 0;
  
  boleta.value.forEach(materia => {
    const nota = materia[keyPeriodo]; 
    if (nota && nota !== '-') {
      suma += parseFloat(nota);
      count++;
    }
  });

  return count > 0 ? (suma / count).toFixed(1) : '-';
};

const promedioVerticalP1 = computed(() => calcularPromedioVertical('p1'));
const promedioVerticalP2 = computed(() => calcularPromedioVertical('p2'));
const promedioVerticalP3 = computed(() => calcularPromedioVertical('p3'));

const abrirHistorial = async () => {
  showHistorialModal.value = true;
  loadingHistorial.value = true;
  datosHistorial.value = null;
  
  try {
    const res = await axios.get(`/matriculas?user_id=${authStore.user.id}`);
    const anios = [...new Set(res.data.map((m) => m.anio))].sort().reverse();
    aniosDisponibles.value = anios;

    if (aniosDisponibles.value.length > 0) {
        anioSeleccionado.value = aniosDisponibles.value[0];
        await cargarDatosHistorial();
    }
  } catch (e) { console.error(e); }
  finally { loadingHistorial.value = false; }
};

const cargarDatosHistorial = async () => {
    if (!anioSeleccionado.value) return;
    loadingHistorial.value = true;
    
    try {
        const res = await axios.get(`/boleta-historial?user_id=${authStore.user.id}&anio=${anioSeleccionado.value}`);
        datosHistorial.value = res.data; 
    } catch (e) {
        console.error(e);
        datosHistorial.value = null; 
    } finally {
        loadingHistorial.value = false;
    }
};

const verDetalle = (materia, periodo) => {
  detalleMateria.value = materia.nombre;
  detallePeriodo.value = periodo;
  detalleNotas.value = rawNotas.value.filter((n) => 
    n.actividad?.materia_id === materia.id && 
    n.actividad?.periodo === periodo
  ).map((n) => ({
    actividad: n.actividad?.nombre,
    valor: n.valor
  }));
  showDetalleModal.value = true;
};

const getColorNota = (nota) => {
  if (!nota || nota === '-') return '';
  return parseFloat(nota) >= 6 ? 'text-success' : 'text-error';
};
</script>

<style scoped>
.text-success { color: #4CAF50 !important; }
.text-error { color: #F44336 !important; }
</style>
