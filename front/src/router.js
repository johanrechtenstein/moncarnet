import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue';
import GarageView from './views/GarageView.vue';
import ContactView from './views/ContactView.vue';
import MaintenanceView from './views/MaintenanceView.vue';
import ProfilView from './views/ProfilView.vue';
import ResetPasswordView from './views/ResetPasswordView.vue';
import MentionsView from './views/MentionsView.vue';


const routes = [
   {
    path: '/contact', // L'URL que l'on cherche
    name: 'Contact',
    component: ContactView 
  },
  {
    path: '/mentions', // L'URL que l'on cherche
    name: 'mentions',
    component: MentionsView 
  },
   {
    path: '/reset-password/:token', // L'URL que l'on cherche
    name: 'ResetPassword',
    component: ResetPasswordView 
  },
    {
    path: '/profil', // L'URL que l'on cherche
    name: 'Profil',
    component:  ProfilView
  },
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/garage', // L'URL que l'on cherche
    name: 'garage',
    // C'est ici que tu créeras ton futur composant DashboardView.vue
    component: GarageView 
  },
  {
  path: '/maintenance/:id',
  name: 'Maintenance',
  component: MaintenanceView
  }  
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;