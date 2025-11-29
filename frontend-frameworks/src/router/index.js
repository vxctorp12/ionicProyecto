import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginPage.vue')
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/DashboardPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/grados',
    name: 'grados',
    component: () => import('../views/GradosPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/materias',
    name: 'materias',
    component: () => import('../views/MateriasPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/docentes',
    name: 'docentes',
    component: () => import('../views/DocentesPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/alumnos',
    name: 'alumnos',
    component: () => import('../views/AlumnosPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/matriculas',
    name: 'matriculas',
    component: () => import('../views/MatriculasPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/cargas',
    name: 'cargas',
    component: () => import('../views/CargasPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/mis-cursos',
    name: 'mis-cursos',
    component: () => import('../views/DocenteCursosPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/notas/:materiaId/:gradoId/:nombreMateria',
    name: 'notas',
    component: () => import('../views/NotasPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/mis-calificaciones',
    name: 'mis-calificaciones',
    component: () => import('../views/MisNotasPage.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.path === '/login' && authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
