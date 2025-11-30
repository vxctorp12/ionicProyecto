import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import TabsPage from '../views/TabsPage.vue'
import LoginPage from '../views/LoginPage.vue'
import GradosPage from '@/views/GradosPage.vue';
import MateriasPage from '@/views/MateriasPage.vue';
import DocentesPage from '@/views/DocentesPage.vue';
import AlumnosPage from '../views/AlumnosPage.vue';
import MatriculasPage from '@/views/MatriculasPage.vue';
import CargasPage from '@/views/CargasPage.vue';
import DocenteCursosPage from '@/views/DocenteCursosPage.vue';
import NotasPage from '@/views/NotasPage.vue';
import MisNotasPage from '@/views/MisNotasPage.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/mis-notas',
    component: MisNotasPage
  },
  {
    path: '/login',
    component: LoginPage
  },
  {
    path: '/mis-cursos',
    component: DocenteCursosPage
  },
  {
    path: '/grados',
    component: GradosPage
  },
  {

    path: '/notas/:materiaId/:gradoId/:nombreMateria',
    component: NotasPage
  },
  {
    path: '/cargas',
    component: CargasPage
  },
  {
    path: '/materias',
    component: MateriasPage
  },
  {
    path: '/docentes',
    component: DocentesPage
  },
  {
    path: '/alumnos',
    component: AlumnosPage
  },
  {
    path: '/matriculas',
    component: MatriculasPage
  },
  {
    path: '/tabs/',
    component: TabsPage,
    children: [
      {
        path: '',
        redirect: '/tabs/tab1'
      },
      {
        path: 'tab1',
        component: () => import('@/views/Tab1Page.vue')
      },
      {
        path: 'tab2',
        component: () => import('@/views/Tab2Page.vue')
      },
      {
        path: 'tab3',
        component: () => import('@/views/Tab3Page.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router