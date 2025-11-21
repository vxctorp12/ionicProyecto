<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-button @click="router.push('/tabs/tab1')">
            <ion-icon slot="icon-only" :icon="arrowBack"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Mi Boleta</ion-title>
        
        <ion-buttons slot="end">
          <ion-button @click="abrirHistorial">
            <ion-icon slot="icon-only" :icon="timeOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="loading" class="center-content">
        <ion-spinner color="primary"></ion-spinner>
      </div>

      <div v-else>
        <div class="student-header" v-if="infoMatricula">
          <h2>{{ infoMatricula.grado?.nombre }}</h2>
          <p>Ciclo Escolar {{ infoMatricula.anio }}</p>
        </div>

        <div class="summary-grid" v-if="infoMatricula">
          <div class="period-card">
            <span class="p-label">Periodo 1</span>
            <span class="p-value" :class="getColorNota(promedioVerticalP1)">{{ promedioVerticalP1 }}</span>
          </div>
          <div class="period-card">
            <span class="p-label">Periodo 2</span>
            <span class="p-value" :class="getColorNota(promedioVerticalP2)">{{ promedioVerticalP2 }}</span>
          </div>
          <div class="period-card">
            <span class="p-label">Periodo 3</span>
            <span class="p-value" :class="getColorNota(promedioVerticalP3)">{{ promedioVerticalP3 }}</span>
          </div>
        </div>

        <ion-list class="custom-list" v-if="boleta.length > 0">
          <ion-card v-for="materia in boleta" :key="materia.id" class="grade-card">
            <ion-card-header>
              <ion-card-title>{{ materia.nombre }}</ion-card-title>
            </ion-card-header>
            <ion-card-content>
              
              <div class="periodos-grid">
                <div class="periodo-box" @click="verDetalle(materia, 'Periodo 1')" v-ripple>
                  <span>P1</span>
                  <strong :class="getColorNota(materia.p1)">{{ materia.p1 || '-' }}</strong>
                </div>
                <div class="periodo-box" @click="verDetalle(materia, 'Periodo 2')" v-ripple>
                  <span>P2</span>
                  <strong :class="getColorNota(materia.p2)">{{ materia.p2 || '-' }}</strong>
                </div>
                <div class="periodo-box" @click="verDetalle(materia, 'Periodo 3')" v-ripple>
                  <span>P3</span>
                  <strong :class="getColorNota(materia.p3)">{{ materia.p3 || '-' }}</strong>
                </div>
              </div>

              <div class="materia-promedio">
                <small>Toca una nota para ver detalles</small>
              </div>

            </ion-card-content>
          </ion-card>
        </ion-list>

        <div v-if="!infoMatricula && !loading" class="center-content empty-text">
          <p>No estás matriculado en el ciclo actual.</p>
        </div>
      </div>

      <ion-modal :is-open="showHistorialModal" @didDismiss="showHistorialModal = false">
        <ion-header>
          <ion-toolbar>
            <ion-title>Historial Académico</ion-title>
            <ion-buttons slot="end">
              <ion-button @click="showHistorialModal = false">
                <ion-icon :icon="close"></ion-icon>
              </ion-button>
            </ion-buttons>
          </ion-toolbar>
        </ion-header>

        <ion-content class="ion-padding bg-light">
           <div class="year-select-container" v-if="aniosDisponibles.length > 0">
              <ion-label>Seleccionar Año:</ion-label>
              <select v-model="anioSeleccionado" @change="cargarDatosHistorial" class="custom-html-select">
                  <option v-for="anio in aniosDisponibles" :key="anio" :value="anio">{{ anio }}</option>
              </select>
           </div>

           <div v-if="loadingHistorial" class="center-content">
              <ion-spinner></ion-spinner>
           </div>

           <div v-else-if="datosHistorial" class="historial-content">
              <div class="general-average-circle">
                 <h1>{{ datosHistorial.promedioGeneral }}</h1>
                 <span>Promedio Global {{ anioSeleccionado }}</span>
              </div>

              <ion-list class="history-list">
                <ion-item v-for="mat in datosHistorial.materias" :key="mat.nombre" lines="full">
                  <ion-label>
                    <h2>{{ mat.nombre }}</h2>
                  </ion-label>
                  <div slot="end" class="final-grade" :class="getColorNota(mat.final)">
                    {{ mat.final }}
                  </div>
                </ion-item>
              </ion-list>
           </div>
           
           <div v-else class="center-content empty-text">
             <p>Selecciona un año para ver el historial.</p>
           </div>
        </ion-content>
      </ion-modal>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { onIonViewWillEnter, modalController } from '@ionic/vue';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonButton,
  IonList, IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonIcon, IonSpinner,
  IonModal, IonItem, IonLabel 
} from '@ionic/vue';
import { arrowBack, timeOutline, close } from 'ionicons/icons';
import DetalleNotasModal from '@/components/DetalleNotasModal.vue';

const router = useRouter();
const authStore = useAuthStore();

// Estado Principal
const loading = ref(true);
const infoMatricula = ref<any>(null);
const boleta = ref<any[]>([]);
const rawNotas = ref<any[]>([]);

// Estado Historial
const showHistorialModal = ref(false);
const loadingHistorial = ref(false);
const aniosDisponibles = ref<number[]>([]); // Se llenará desde API
const anioSeleccionado = ref<number | null>(null);
const datosHistorial = ref<any>(null);

onIonViewWillEnter(() => loadData());

const loadData = async () => {
  loading.value = true;
  try {
    // 1. Cargar Matrícula Actual
    const resMatricula = await axios.get(`/matriculas?user_id=${authStore.user.id}&activo=true`); // Asumimos un filtro 'activo' o tomamos el último
    
    if (resMatricula.data.length > 0) {
      // Tomamos la matricula más reciente (Ciclo actual)
      infoMatricula.value = resMatricula.data[0]; 
      const matriculaId = infoMatricula.value.id;
      
      // 2. Cargar Notas
      const resNotas = await axios.get(`/notas?matricula_id=${matriculaId}`);
      rawNotas.value = resNotas.data;

      // 3. Procesar Boleta
      if (infoMatricula.value.grado?.materias) {
        boleta.value = infoMatricula.value.grado.materias.map((materia: any) => {
          const notasDeLaMateria = rawNotas.value.filter((n: any) => n.actividad?.materia_id === materia.id);
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

// --- LÓGICA DE PROMEDIOS ---

// 1. Promedio de una materia en un periodo (Horizontal)
const calcularPromedioMateria = (notas: any[], periodo: string) => {
  const notasDelPeriodo = notas.filter((n: any) => n.actividad?.periodo === periodo);
  if (notasDelPeriodo.length === 0) return null;
  const suma = notasDelPeriodo.reduce((acc: number, curr: any) => acc + parseFloat(curr.valor), 0);
  return (suma / notasDelPeriodo.length).toFixed(1);
};

// 2. Promedios Verticales (Computed) - Promedio del periodo de TODAS las materias
const promedioVerticalP1 = computed(() => calcularPromedioVertical('p1'));
const promedioVerticalP2 = computed(() => calcularPromedioVertical('p2'));
const promedioVerticalP3 = computed(() => calcularPromedioVertical('p3'));

const calcularPromedioVertical = (keyPeriodo: string) => {
  if (boleta.value.length === 0) return '-';
  
  let suma = 0;
  let count = 0;
  
  boleta.value.forEach(materia => {
    const nota = materia[keyPeriodo]; // Accedemos a 'p1', 'p2' o 'p3'
    if (nota && nota !== '-') {
      suma += parseFloat(nota);
      count++;
    }
  });

  return count > 0 ? (suma / count).toFixed(1) : '-';
};

// --- HISTORIAL ---

const abrirHistorial = async () => {
  showHistorialModal.value = true;
  loadingHistorial.value = true;
  datosHistorial.value = null;
  
  try {
    // 1. Consultar años donde el alumno tuvo matrícula
    // Endpoint sugerido: /matriculas/anios-anteriores?user_id=...
    const res = await axios.get(`/matriculas?user_id=${authStore.user.id}`); 
    
    // Extraemos años únicos y excluimos el actual si es necesario
    const anios = [...new Set(res.data.map((m: any) => m.anio))].sort().reverse();
    aniosDisponibles.value = anios as number[];

    if (aniosDisponibles.value.length > 0) {
        // Seleccionar automáticamente el año anterior si existe, o el primero de la lista
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
        // Endpoint sugerido: /reportes/boleta-final?user_id=X&anio=Y
        // Aquí debes conectar con tu backend real que calcule el promedio final
        const res = await axios.get(`/boleta-historial?user_id=${authStore.user.id}&anio=${anioSeleccionado.value}`);
        datosHistorial.value = res.data; 
    } catch (e) {
        console.error(e);
        // Fallback visual si falla la API
        datosHistorial.value = null; 
    } finally {
        loadingHistorial.value = false;
    }
};

// --- UTILS & UI ---

const verDetalle = async (materia: any, periodo: string) => {
  const notasDetalle = rawNotas.value.filter((n: any) => 
    n.actividad?.materia_id === materia.id && 
    n.actividad?.periodo === periodo
  ).map((n:any) => ({
    actividad: n.actividad?.nombre,
    valor: n.valor
  }));

  const modal = await modalController.create({
    component: DetalleNotasModal,
    componentProps: { materia: materia.nombre, periodo: periodo, notas: notasDetalle },
    breakpoints: [0, 0.5, 0.8],
    initialBreakpoint: 0.5
  });
  modal.present();
};

const getColorNota = (nota: any) => {
  if (!nota || nota === '-') return '';
  return parseFloat(nota) >= 6 ? 'text-green' : 'text-red';
};
</script>

<style scoped>
.bg-light { --background: #F4F6F8; }
.center-content { display: flex; justify-content: center; margin-top: 50px; }
.empty-text { color: #999; }

.student-header { text-align: center; margin-bottom: 15px; }
.student-header h2 { margin: 0; font-weight: 800; color: #333; }
.student-header p { margin: 5px 0 0; color: #666; }

/* --- GRID DE PROMEDIOS SUPERIORES (P1, P2, P3) --- */
.summary-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px;
  margin-bottom: 20px;
}
.period-card {
  background: white;
  border-radius: 10px;
  padding: 10px 5px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.p-label { display: block; font-size: 0.75rem; color: #888; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
.p-value { font-size: 1.4rem; font-weight: 800; }

/* --- TARJETAS DE MATERIAS --- */
.grade-card { margin-bottom: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); background: white; }
.grade-card ion-card-title { font-size: 1rem; font-weight: 700; color: #444; }
.periodos-grid { display: flex; justify-content: space-around; background: #FAFAFA; padding: 10px; border-radius: 8px; }

.periodo-box { text-align: center; padding: 5px 15px; border-radius: 8px; cursor: pointer; width: 30%; }
.periodo-box:active { background: #E0E0E0; }
.periodo-box span { display: block; font-size: 0.8rem; color: #888; margin-bottom: 2px; }
.periodo-box strong { font-size: 1.1rem; }

.materia-promedio { text-align: center; font-size: 0.75rem; color: #BBB; border-top: 1px solid #F0F0F0; padding-top: 8px; margin-top: 8px; }

/* --- ESTILOS MODAL HISTORIAL --- */
.year-select-container { margin-bottom: 20px; display: flex; align-items: center; gap: 10px; background: white; padding: 10px; border-radius: 10px; }
.custom-html-select { flex: 1; padding: 8px; border: 1px solid #DDD; border-radius: 6px; font-size: 1rem; background: white; }

.general-average-circle {
  width: 140px; height: 140px;
  background: var(--ion-color-primary);
  border-radius: 50%;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  color: white;
  margin: 0 auto 30px auto;
  box-shadow: 0 8px 20px rgba(var(--ion-color-primary-rgb), 0.4);
}
.general-average-circle h1 { font-size: 3.5rem; margin: 0; font-weight: 800; line-height: 1; }
.general-average-circle span { font-size: 0.8rem; opacity: 0.9; margin-top: 5px; }

.history-list ion-item { --padding-start: 0; }
.final-grade { font-size: 1.2rem; font-weight: bold; }

/* Colores */
.text-green { color: var(--ion-color-success); }
.text-red { color: var(--ion-color-danger); }
</style>