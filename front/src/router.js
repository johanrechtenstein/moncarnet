import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue';
import GarageView from './views/GarageView.vue';
import ContactView from './views/ContactView.vue';
import MaintenanceView from './views/MaintenanceView.vue';

const routes = [
   {
    path: '/contact', // L'URL que l'on cherche
    name: 'contact',
    component: ContactView 
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