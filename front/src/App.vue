<template>
  <div class="main-container">
    <Navbar />
  

  </div>
</template>

<script setup>
// 1. On importe le fichier du composant
import Navbar from './components/Navbar.vue'
</script>

<style>

.main-container {
  /* On pointe vers ton image dans assets */
  background-image: url('./assets/ACM.png');
  
  /* L'image couvre tout sans se déformer */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-color:black;
  
  /* On force la hauteur à 100% de la fenêtre */
  min-height: 100vh;
  
  
  /* On ajoute un léger voile noir pour que le texte soit lisible */
  /* (C'est le dégradé que l'on voit sur ta maquette) */
  /*box-shadow: inset 0 0 0 2000px rgba(0, 0, 0, 0.4);*/

  display: flex;
  flex-direction: column;
}
</style>