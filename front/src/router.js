import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  // On pourra ajouter { path: '/inscription', component: RegistrationView } plus tard !
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;