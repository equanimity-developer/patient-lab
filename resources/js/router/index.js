import { createRouter, createWebHistory } from 'vue-router';
import LoginComponent from '@/components/patient/LoginComponent.vue';
import ResultsComponent from '@/components/patient/ResultsComponent.vue';
import { isAuthenticated } from '@/utils/auth.js';

const routes = [
  {
    path: '/',
    redirect: to => {
      return isAuthenticated() ? '/patient/results' : '/login';
    }
  },
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

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    next('/login');
  } else if (to.path === '/login' && isAuthenticated()) {
    next('/patient/results');
  } else {
    next();
  }
});

export default router;
