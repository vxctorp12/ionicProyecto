<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="primary">
        <ion-buttons slot="start">
          <ion-back-button default-href="/mis-cursos"></ion-back-button>
        </ion-buttons>
        <ion-title>{{ nombreMateria }}</ion-title>
      </ion-toolbar>
      
      <ion-toolbar color="light">
        <ion-segment v-model="periodoSeleccionado" @ionChange="cargarActividades">
          <ion-segment-button value="Periodo 1"><ion-label>P1</ion-label></ion-segment-button>
          <ion-segment-button value="Periodo 2"><ion-label>P2</ion-label></ion-segment-button>
          <ion-segment-button value="Periodo 3"><ion-label>P3</ion-label></ion-segment-button>
        </ion-segment>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding bg-light">
      
      <div v-if="!vistaCalificar">
        <ion-list-header>
          <ion-label>Actividades del {{ periodoSeleccionado }}</ion-label>
          <ion-button size="small" @click="crearActividad">
            <ion-icon :icon="add" slot="start"></ion-icon> Nueva
          </ion-button>
        </ion-list-header>

        <ion-list class="custom-list">
          <ion-item v-for="act in actividades" :key="act.id" button detail @click="abrirActividad(act)">
            <ion-label>
              <h2>{{ act.nombre }}</h2>
              <p>Toque para calificar</p>
            </ion-label>
          </ion-item>
        </ion-list>
        
        <div v-if="actividades.length === 0" class="empty-text text-center">
          <p>No hay actividades creadas. Agrega una (ej. Examen).</p>
        </div>
      </div>

      <div v-else>
        <div class="sub-header">
          <ion-button fill="clear" @click="vistaCalificar = false">
            <ion-icon :icon="arrowBack"></ion-icon> Volver
          </ion-button>
          <h3>Calificando: {{ actividadActual.nombre }}</h3>
        </div>

        <ion-list>
          <ion-card v-for="alumno in listaAlumnos" :key="alumno.matricula_id" class="student-card">
            <ion-item lines="none">
              <ion-label>
                <h2>{{ alumno.nombre }}</h2>
              </ion-label>
              <div slot="end" class="grade-input-wrapper">
                <ion-input 
                  type="number" placeholder="-" class="grade-input"
                  v-model="alumno.nota_valor" :min="0" :max="10"
                  @ionBlur="guardarNota(alumno)" 
                ></ion-input>
              </div>
            </ion-item>
          </ion-card>
        </ion-list>
        
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonBackButton,
  IonList, IonItem, IonLabel, IonSegment, IonSegmentButton, IonButton, IonIcon, 
  IonListHeader, IonCard, IonInput, alertController, toastController 
} from '@ionic/vue';
import { onIonViewWillEnter } from '@ionic/vue';
import { add, arrowBack } from 'ionicons/icons';

const route = useRoute();
const materiaId = route.params.materiaId;
const gradoId = route.params.gradoId;
const nombreMateria = route.params.nombreMateria;

const periodoSeleccionado = ref('Periodo 1');
const actividades = ref<any[]>([]);
const vistaCalificar = ref(false);
const actividadActual = ref<any>(null);
const listaAlumnos = ref<any[]>([]);

onIonViewWillEnter(() => cargarActividades());

// 1. Cargar Actividades (Examen 1, Tarea 1...)
const cargarActividades = async () => {
  vistaCalificar.value = false;
  try {
    const res = await axios.get(`/actividades?materia_id=${materiaId}&periodo=${periodoSeleccionado.value}`);
    actividades.value = res.data;
  } catch (e) { console.error(e); }
};

// 2. Crear Nueva Actividad
const crearActividad = async () => {
  const alert = await alertController.create({
    header: 'Nueva Actividad',
    inputs: [{ name: 'nombre', type: 'text', placeholder: 'Ej. Examen Parcial' }],
    buttons: [
      { text: 'Cancelar', role: 'cancel' },
      { text: 'Crear', handler: async (data) => {
          if(data.nombre) {
            await axios.post('/actividades', {
              materia_id: materiaId,
              periodo: periodoSeleccionado.value,
              nombre: data.nombre
            });
            cargarActividades();
          }
        }
      }
    ]
  });
  await alert.present();
};

// 3. Abrir lista de alumnos para calificar una actividad
const abrirActividad = async (actividad: any) => {
  actividadActual.value = actividad;
  
  // Cargar alumnos y sus notas para ESTA actividad
  const resMatriculas = await axios.get(`/matriculas?grado_id=${gradoId}`);
  const resNotas = await axios.get(`/notas?actividad_id=${actividad.id}`); // Backend debe soportar este filtro

  listaAlumnos.value = resMatriculas.data.map((mat: any) => {
    const nota = resNotas.data.find((n: any) => n.matricula_id === mat.id);
    return {
      matricula_id: mat.id,
      nombre: mat.user.name,
      nota_valor: nota ? nota.valor : null
    };
  });

  vistaCalificar.value = true;
};

// 4. Guardar Nota
const guardarNota = async (alumno: any) => {
  if (alumno.nota_valor === null || alumno.nota_valor === '') return;
  try {
    await axios.post('/notas', {
      matricula_id: alumno.matricula_id,
      actividad_id: actividadActual.value.id,
      valor: alumno.nota_valor
    });
  } catch (e) { console.error(e); }
};
</script>

<style scoped>
.bg-light { --background: #F4F6F8; }
.grade-input-wrapper { width: 60px; border: 1px solid #CCC; border-radius: 8px; background: white; }
.grade-input { text-align: center; font-weight: bold; --padding-start: 5px; }
.sub-header { display: flex; align-items: center; justify-content: space-between; padding-right: 10px; }
.text-center { text-align: center; margin-top: 20px; color: #666; }
</style>