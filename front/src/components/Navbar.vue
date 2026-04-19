<template>
  <nav class="navbar">
    <div class="logo">
      <router-link :to="isLoggedIn ? '/garage' : '/'">  <!-- expression ternaire -->
        <img src="../assets/logo2.png" alt="Logo MG" class="logo-img">
      </router-link>
    </div>

    <div class="hamburger" @click="toggleMenu" :class="{ 'is-active': isMenuOpen }">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <div class="nav-links" :class="{ 'open': isMenuOpen }">
      <router-link to="/contact" class="nav-item" @click="closeMenu">Contact</router-link>
      <router-link v-if="isLoggedIn" to="/profil" class="nav-item" @click="closeMenu">Profil</router-link>
      <router-link v-if="isLoggedIn" to="/garage" class="nav-item" @click="closeMenu">Mon garage</router-link>
      <router-link v-if="!isLoggedIn" to="/" class="nav-item" @click="closeMenu">Accueil</router-link>

      <button v-if="isLoggedIn" @click="handleLogout" class="btn-inscription">
        Déconnexion
      </button>

      <button v-else @click="handleRegisterClick" class="btn-inscription">
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
const isMenuOpen = ref(false)
const emit = defineEmits(['open-register'])

const checkLoginStatus = () => {
  isLoggedIn.value = !!localStorage.getItem('user-token')
}


  const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}

const handleRegisterClick = () => {
  closeMenu()
  emit('open-register')
}


// 1. On vérifie au chargement initial
onMounted(() => {
  checkLoginStatus()
})


const handleLogout = async () => {
  closeMenu()
  
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

watch(() => route.path, () => {
  checkLoginStatus()
  closeMenu()
})
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


/* --- menu burger --- */

.hamburger {
  display: none; /* Caché sur PC */
  flex-direction: column;
  gap: 6px;
  cursor: pointer;
  z-index: 1000;
}

.hamburger span {
  width: 30px;
  height: 3px;
  background-color: white;
  transition: all 0.3s ease;
}

/* Animation du hamburger en "X" quand il est actif */
.hamburger.is-active span:nth-child(1) { transform: translateY(9px) rotate(45deg); }
.hamburger.is-active span:nth-child(2) { opacity: 0; }
.hamburger.is-active span:nth-child(3) { transform: translateY(-9px) rotate(-45deg); }

@media (max-width: 900px) {
  .hamburger { display: flex; }

  .nav-links {
    position: fixed;
    top: 0;
    right: -100%; /* Caché à droite */
    width: 100%;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.95); /* Fond sombre */
    flex-direction: column;
    justify-content: center;
    gap: 30px;
    transition: 0.4s ease;
  }

  .nav-links.open {
    right: 0; /* Apparaît ! */
  }

  .nav-item { font-size: 1.5rem; }
}
</style>