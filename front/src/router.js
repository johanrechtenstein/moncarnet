import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue'; // On garde la home en direct pour un affichage immédiat

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/contact',
    name: 'Contact',
    component: () => import('./views/ContactView.vue') // Lazy Loading
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('./views/ForgotPasswordView.vue')
  },
  {
    path: '/mentions',
    name: 'mentions',
    component: () => import('./views/MentionsView.vue')
  },
  {
    path: '/reset-password/',
    name: 'ResetPassword',
    component: () => import('./views/ResetPasswordView.vue')
  },
  {
    path: '/profil',
    name: 'Profil',
    component: () => import('./views/ProfilView.vue')
  },
  {
    path: '/garage',
    name: 'garage',
    component: () => import('./views/GarageView.vue')
  },
  {
    path: '/maintenance/:id',
    name: 'Maintenance',
    component: () => import('./views/MaintenanceView.vue')
  }  
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;