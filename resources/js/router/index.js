import { createRouter, createWebHistory } from 'vue-router';
import LoginComponent from '../components/patient/LoginComponent.vue';
import ResultsComponent from '../components/patient/ResultsComponent.vue';

const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginComponent
  },
  {
    path: '/patient/results',
    name: 'results',
    component: ResultsComponent,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard for protected routes
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !localStorage.getItem('token')) {
    next('/login');
  } else {
    next();
  }
});

export default router; 