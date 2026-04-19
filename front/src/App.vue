<template>
  <div class="main-container">
    <Navbar @open-register="showRegister = true" />

    <div class="router-content">
    
      <router-view v-slot="{ Component }">
      <transition name="nfs" mode="out-in">
        <component :is="Component" />
      </transition>
      </router-view>
    </div>
    <RegisterModal :isOpen="showRegister" @close="showRegister = false" />
    <Footer />
  </div>
</template>

<script setup>
// 1. On importe le fichier du composant
import { ref } from 'vue';
import Navbar from './components/Navbar.vue';
import RegisterModal from './components/RegisterModal.vue';
import Footer from './components/Footer.vue';
const showRegister = ref(false);
</script>

<style>

.main-container {
  /* On pointe vers ton image dans assets */
  background-image: url('./assets/ACM.png');
  
  /* L'image couvre tout sans se déformer */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed; /* L'image ne défile pas */
  background-color:black;
  
  /* On force la hauteur à 100% de la fenêtre */
  min-height: 100vh;
  position: relative;
  
  
  /* On ajoute un léger voile noir pour que le texte soit lisible */
  /* (C'est le dégradé que l'on voit sur ta maquette) */
  /*box-shadow: inset 0 0 0 2000px rgba(0, 0, 0, 0.4);*/

  display: flex;
  flex-direction: column;
}

.router-content {
  position: relative;
  z-index: 10;
  flex: 1; /* Cela force le contenu à prendre toute la place entre le header et le footer */
  display: flex;
  justify-content: center; 
  align-items: center;     
  width: 100%;
  overflow: hidden;
}


/* --- TRANSITION STYLE JEU VIDÉO --- */

/* 1. Pendant que ça bouge */
.nfs-enter-active,
.nfs-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); /* Transition "nerveuse" */
}

/* 2. État de départ (Arrivée de la droite) */
.nfs-enter-from {
  opacity: 0;
  transform: translateX(100px); /* Arrive de la droite */
}

/* 3. État d'arrivée (Départ vers la gauche) */
.nfs-leave-to {
  opacity: 0;
  transform: translateX(-100px); /* Part vers la gauche */
}

/*désactive effet pour accessibilité*/
@media (prefers-reduced-motion: reduce) {
  .nfs-enter-active, .nfs-leave-active {
    transition: none !important;
  }
}


</style>