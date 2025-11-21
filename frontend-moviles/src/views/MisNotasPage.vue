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
          <div class="promedio-card">
            <span>Promedio General</span>
            <strong>{{ promedioGeneral }}</strong>
          </div>
        </div>

        <ion-list class="custom-list">
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

        <div v-if="!infoMatricula" class="center-content empty-text">
          <p>No estás matriculado en ningún grado.</p>
        </div>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { onIonViewWillEnter, modalController } from '@ionic/vue'; // Importamos modalController
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonButton,
  IonList, IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonIcon, IonSpinner 
} from '@ionic/vue';
import { arrowBack } from 'ionicons/icons';
import DetalleNotasModal from '@/components/DetalleNotasModal.vue'; // <--- IMPORTAR MODAL

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(true);
const infoMatricula = ref<any>(null);
const boleta = ref<any[]>([]);
const rawNotas = ref<any[]>([]); // Guardamos todas las notas crudas aquí

onIonViewWillEnter(() => loadData());

const loadData = async () => {
  loading.value = true;
  try {
    const resMatricula = await axios.get(`/matriculas?user_id=${authStore.user.id}`);
    
    if (resMatricula.data.length > 0) {
      infoMatricula.value = resMatricula.data[0];
      const matriculaId = infoMatricula.value.id;
      const materiasDelGrado = infoMatricula.value.grado.materias;

      const resNotas = await axios.get(`/notas?matricula_id=${matriculaId}`);
      rawNotas.value = resNotas.data; // Guardamos para usar en el detalle

      boleta.value = materiasDelGrado.map((materia: any) => {
        const notasDeLaMateria = rawNotas.value.filter((n: any) => n.actividad?.materia_id === materia.id);

        return {
          id: materia.id,
          nombre: materia.nombre,
          p1: calcularPromedioPeriodo(notasDeLaMateria, 'Periodo 1'),
          p2: calcularPromedioPeriodo(notasDeLaMateria, 'Periodo 2'),
          p3: calcularPromedioPeriodo(notasDeLaMateria, 'Periodo 3'),
        };
      });
    }
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

// ABRIR MODAL DE DETALLE
const verDetalle = async (materia: any, periodo: string) => {
  // Filtramos las notas específicas de esa materia y periodo
  const notasDetalle = rawNotas.value.filter((n: any) => 
    n.actividad?.materia_id === materia.id && 
    n.actividad?.periodo === periodo
  ).map((n:any) => ({
    actividad: n.actividad?.nombre,
    valor: n.valor
  }));

  const modal = await modalController.create({
    component: DetalleNotasModal,
    componentProps: {
      materia: materia.nombre,
      periodo: periodo,
      notas: notasDetalle
    },
    breakpoints: [0, 0.5, 0.8], // Efecto hoja deslizable (Sheet Modal)
    initialBreakpoint: 0.5
  });

  modal.present();
};

// Funciones de cálculo (Igual que antes)
const calcularPromedioPeriodo = (notas: any[], periodo: string) => {
  const notasDelPeriodo = notas.filter((n: any) => n.actividad?.periodo === periodo);
  if (notasDelPeriodo.length === 0) return null; // Retornamos null en vez de '-' para validar mejor
  const suma = notasDelPeriodo.reduce((acc: number, curr: any) => acc + parseFloat(curr.valor), 0);
  return (suma / notasDelPeriodo.length).toFixed(1);
};

const promedioGeneral = computed(() => {
  if (boleta.value.length === 0) return '-';
  let suma = 0;
  let cont = 0;
  boleta.value.forEach(m => {
    [m.p1, m.p2, m.p3].forEach(v => {
      if (v) { suma += parseFloat(v); cont++; }
    });
  });
  return cont > 0 ? (suma / cont).toFixed(1) : '-';
});

const getColorNota = (nota: any) => {
  if (!nota) return '';
  return parseFloat(nota) >= 6 ? 'text-green' : 'text-red';
};
</script>

<style scoped>
.bg-light { --background: #F4F6F8; }
.student-header { text-align: center; margin-bottom: 20px; }
.student-header h2 { margin: 0; font-weight: 800; color: #333; }
.promedio-card { background: var(--ion-color-primary); color: white; padding: 15px; border-radius: 12px; margin-top: 15px; display: inline-block; min-width: 150px; box-shadow: 0 4px 10px rgba(var(--ion-color-primary-rgb), 0.3); }
.promedio-card span { display: block; font-size: 0.9rem; opacity: 0.9; }
.promedio-card strong { font-size: 1.8rem; display: block; margin-top: 5px; }

.grade-card { margin-bottom: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); background: white; }
.grade-card ion-card-title { font-size: 1.1rem; font-weight: 700; color: #444; }

.periodos-grid { display: flex; justify-content: space-around; margin-bottom: 5px; background: #FAFAFA; padding: 10px; border-radius: 8px; }


/* Estilo botón clickable */
.periodo-box { 
  text-align: center; 
  padding: 5px 15px; 
  border-radius: 8px; 
  transition: background 0.2s;
  cursor: pointer;
}
.periodo-box:active { background: #E0E0E0; } /* Feedback visual al tocar */

.periodo-box span { display: block; font-size: 0.8rem; color: #888; margin-bottom: 2px; }
.periodo-box strong { font-size: 1.1rem; }

.text-green { color: var(--ion-color-success); }
.text-red { color: var(--ion-color-danger); }

.materia-promedio { text-align: center; font-size: 0.8rem; color: #999; border-top: 1px solid #EEE; padding-top: 10px; margin-top: 5px; }
.center-content { display: flex; justify-content: center; margin-top: 50px; }
.empty-text { color: #999; }
</style>