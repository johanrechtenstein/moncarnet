<template>
  <div>
    <h1>Bienvenue dans le garage de {{ nomUtilisateur }}</h1>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const nomUtilisateur = ref('')

// onMounted s'exécute dès que la page s'affiche
onMounted(() => {
  // On va chercher le pseudo qu'on a rangé dans le localStorage
  const savedPseudo = localStorage.getItem('user-pseudo');
  
  if (savedPseudo) {
    nomUtilisateur.value = savedPseudo;
  } else {
    nomUtilisateur.value = 'Invité';
  }
})
</script>

<style scoped>
/* Ton futur style ici pour changer le fond ! */
h1 {
  color: white;
  text-align: center;
  margin-top: 50px;
}
</style>