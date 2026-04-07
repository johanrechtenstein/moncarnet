<template>
  <nav class="navbar">
    <div class="logo">
      <router-link to="/">
        <img src="../assets/logo2.png" alt="Logo MG" class="logo-img">
      </router-link>
    </div>

    <div class="nav-links">
      <a href="#" class="nav-item">Contact</a>

      <button v-if="isLoggedIn" @click="logout" class="btn-inscription">
        Déconnexion
      </button>

      <button v-else class="btn-inscription">
        Inscription
      </button>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const isLoggedIn = ref(false)

const checkLoginStatus = () => {
  isLoggedIn.value = !!localStorage.getItem('user-token')
}

// 1. On vérifie au chargement initial
onMounted(() => {
  checkLoginStatus()
})

// 2. MAGIE : On surveille chaque changement de page (route)
// Dès que l'URL change (ex: de / vers /garage), on revérifie le token
watch(() => route.path, () => {
  checkLoginStatus()
})

const logout = async () => {
  try {
    const token = localStorage.getItem('user-token')
    
    // On prévient l'API Laravel qu'on se déconnecte
    await axios.post('http://127.0.0.1:8000/api/logout', {}, {
      headers: { Authorization: `Bearer ${token}` }
    })
  } catch (error) {
    console.error("Erreur lors de la déconnexion", error)
  } finally {
    // Dans tous les cas, on nettoie le navigateur et on redirige
    localStorage.removeItem('user-token')
    localStorage.removeItem('user-pseudo')
    isLoggedIn.value = false
    router.push('/')
  }
}
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between; /* Logo à gauche, Liens à droite */
  align-items: center; /* Centré verticalement */
  padding: 30px 60px; /* Un peu plus d'espace */
  
  background: linear-gradient(
    to bottom,            /* Direction du dégradé (du haut vers le bas) */
    rgba(0, 0, 0, 1) 80%,   
    rgba(0, 0, 0, 0) 100% 
  );
  
  /* On positionne la navbar en haut de l'écran */
  /*position: absolute;*/
  top: 0;
  left: 0;
  width: 100%;
  z-index: 999; /* Assure qu'elle est bien DEVANT l'image de fond */
  box-sizing: border-box; /* Évite que le padding ne déforme la largeur */
  pointer-events: auto;
}

.logo-img {
  height: 50px; /* Ajuste la hauteur selon tes envies */
  width: auto;  /* Garde les proportions */
  display: block;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 40px; /* Plus d'espace entre les menus */
}

.nav-item {
  color: white;
  text-decoration: none;
  font-weight: 500;
  font-size: 1.1rem;
  transition: all 0.3s ease;
}

.nav-item:hover {
  color:#FF6B35;
  transform: translateY(-2px);
}

.btn-inscription {
  /* Fond orange saumon, texte noir */
  background-color: #FF6B35; 
  color: white;
  
  border: none;
  padding: 12px 28px; /* Un bouton un peu plus grand */
  border-radius: 25px;
  font-weight: bold;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-inscription:hover {
  background-color: white; /* Un orange un peu plus sombre au survol */
  transform: translateY(-2px); /* Un léger mouvement vers le haut */
  color:#FF6B35;
}
</style>