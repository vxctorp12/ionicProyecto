<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar color="primary" dark>
            <v-btn icon @click="router.push('/dashboard')">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Mis Cursos</v-toolbar-title>
          </v-toolbar>

          <v-card-text>
            <div v-if="loading" class="text-center">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </div>

            <v-row v-else>
              <v-col v-for="carga in cursos" :key="carga.id" cols="12" sm="6" md="4">
                <v-card hover @click="irACalificar(carga)" class="mx-auto">
                  <v-card-item>
                    <template v-slot:prepend>
                      <v-avatar color="primary" class="mr-2">
                        <v-icon color="white">mdi-human-male-board</v-icon>
                      </v-avatar>
                    </template>
                    <v-card-title>{{ carga.materia?.nombre }}</v-card-title>
                    <v-card-subtitle>
                      <v-icon size="small" class="mr-1">mdi-layers</v-icon>
                      {{ carga.materia?.grado?.nombre }}
                    </v-card-subtitle>
                  </v-card-item>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" variant="text">Calificar</v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>

            <div v-if="!loading && cursos.length === 0" class="text-center mt-4">
              <p class="text-medium-emphasis">No tienes materias asignadas.</p>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const cursos = ref([]);
const loading = ref(true);

const loadCursos = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/cargas?user_id=${authStore.user.id}`);
    cursos.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadCursos();
});

const irACalificar = (carga) => {
  const materiaId = carga.materia_id;
  const gradoId = carga.materia.grado_id;
  const nombre = carga.materia.nombre;
  
  router.push(`/notas/${materiaId}/${gradoId}/${nombre}`);
};
</script>
