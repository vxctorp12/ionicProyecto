<template>
  <v-container fluid class="dashboard-container fill-height align-start">
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        
        <!-- Welcome Header -->
        <div class="mb-8 mt-4">
          <div class="d-flex justify-space-between align-center mb-2">
            <div>
              <h1 class="text-h4 font-weight-bold text-white">Hola, {{ user?.name }}</h1>
              <p class="text-subtitle-1 text-medium-emphasis">{{ user?.email }}</p>
            </div>
            <v-chip color="primary" variant="flat" class="font-weight-bold">
              {{ getRoleName(user?.role_id) }}
            </v-chip>
          </div>
          <v-divider class="mb-6"></v-divider>
        </div>

        <v-row v-if="user" justify="center">
          <template v-if="isAdmin">
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Grados"
                icon="mdi-layers"
                color="primary"
                @click="router.push('/grados')"
              />
            </v-col>
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Materias"
                icon="mdi-book-open-variant"
                color="secondary"
                @click="router.push('/materias')"
              />
            </v-col>
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Docentes"
                icon="mdi-account-tie"
                color="info"
                @click="router.push('/docentes')"
              />
            </v-col>
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Alumnos"
                icon="mdi-account"
                color="success"
                @click="router.push('/alumnos')"
              />
            </v-col>
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Inscripciones"
                icon="mdi-card-account-details"
                color="warning"
                @click="router.push('/matriculas')"
              />
            </v-col>
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Carga Académica"
                icon="mdi-briefcase"
                color="teal"
                @click="router.push('/cargas')"
              />
            </v-col>
          </template>

          <template v-if="isDocente">
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Mis Cursos"
                icon="mdi-human-male-board"
                color="indigo"
                description="Gestiona tus cursos y calificaciones"
                @click="router.push('/mis-cursos')"
              />
            </v-col>
          </template>

          <template v-if="isAlumno">
            <v-col cols="12" sm="6" md="4">
              <dashboard-card
                title="Mis Calificaciones"
                icon="mdi-school"
                color="deep-purple"
                description="Consulta tu historial académico"
                @click="router.push('/mis-calificaciones')"
              />
            </v-col>
          </template>
        </v-row>

        <div v-else class="text-center mt-12">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>

        <div class="d-flex justify-center mt-12 gap-4">
          <change-password-dialog />
          
          <v-btn
            color="error"
            variant="text"
            prepend-icon="mdi-logout"
            @click="handleLogout"
          >
            Cerrar Sesión
          </v-btn>
        </div>

      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import DashboardCard from '../../components/DashboardCard.vue';
import ChangePasswordDialog from '../../components/ChangePasswordDialog.vue';

const authStore = useAuthStore();
const router = useRouter();

const user = computed(() => authStore.user);

const isAdmin = computed(() => user.value && user.value.role_id === 1);
const isDocente = computed(() => user.value && user.value.role_id === 2);
const isAlumno = computed(() => user.value && user.value.role_id === 3);

const getRoleName = (roleId) => {
  switch (roleId) {
    case 1: return 'Administrador';
    case 2: return 'Docente';
    case 3: return 'Alumno';
    default: return 'Usuario';
  }
};

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};
</script>

<style scoped>
.dashboard-container {
  background-color: #0F172A; /* Dark Navy Background */
}
</style>
