<template>
  <ion-header>
    <ion-toolbar>
      <ion-title>Detalle {{ periodo }}</ion-title>
      <ion-buttons slot="end">
        <ion-button @click="cerrar">Cerrar</ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>

  <ion-content class="ion-padding">
    <div class="header-info">
      <h2>{{ materia }}</h2>
      <p>Desglose de calificaciones</p>
    </div>

    <ion-list lines="full">
      <ion-item v-for="(item, index) in notas" :key="index">
        <ion-label>
          <h3>{{ item.actividad }}</h3>
        </ion-label>
        <div slot="end" class="nota-badge" :class="getColor(item.valor)">
          {{ item.valor }}
        </div>
      </ion-item>
    </ion-list>

    <div v-if="notas.length === 0" class="empty-msg">
      No hay actividades registradas.
    </div>
    
    <div class="footer-promedio" v-if="notas.length > 0">
      <span>Promedio del Periodo:</span>
      <strong>{{ promedio }}</strong>
    </div>

  </ion-content>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { modalController } from '@ionic/vue';
import { 
  IonHeader, IonToolbar, IonTitle, IonButtons, IonButton, IonContent, 
  IonList, IonItem, IonLabel 
} from '@ionic/vue';

const props = defineProps<{
  materia: string;
  periodo: string;
  notas: any[];
}>();

const cerrar = () => modalController.dismiss();

const getColor = (valor: string) => {
  return parseFloat(valor) >= 6 ? 'bg-green' : 'bg-red';
};

const promedio = computed(() => {
  if (props.notas.length === 0) return '-';
  const suma = props.notas.reduce((acc, curr) => acc + parseFloat(curr.valor), 0);
  return (suma / props.notas.length).toFixed(1);
});
</script>

<style scoped>
.header-info { text-align: center; margin-bottom: 20px; }
.header-info h2 { margin: 0; font-weight: bold; color: #333; }
.header-info p { margin: 5px 0 0; color: #888; font-size: 0.9rem; }

.nota-badge {
  padding: 5px 10px;
  border-radius: 8px;
  font-weight: bold;
  color: white;
  min-width: 40px;
  text-align: center;
}
.bg-green { background: var(--ion-color-success); }
.bg-red { background: var(--ion-color-danger); }

.empty-msg { text-align: center; color: #999; margin-top: 30px; }

.footer-promedio {
  margin-top: 30px;
  padding: 15px;
  background: #F4F6F8;
  border-radius: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.footer-promedio strong { font-size: 1.2rem; color: var(--ion-color-dark); }
</style>