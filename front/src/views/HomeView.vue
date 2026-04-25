<template>
  <div class="hero-container">
    <div class="hero-text">
      <h1>Votre passion mérite le <span class="highlight">digital</span></h1>
      <p>"Suivez l'<span class="highlight">entretien</span> de votre véhicule en temps réel, car son <span class="highlight">passé</span> vous le rendra dans le <span class="highlight">futur</span>."</p>
    </div>

    <form @submit.prevent="login" class="hero-form">
      <div class="form-group">
        <label>Pseudo :</label>
        <input v-model="pseudo" type="text" placeholder="Pseudo" />
      </div>
      <div class="form-group">
        <label>Mot de passe :</label>
        <input v-model="password" type="password" placeholder="••••••••" />
        <div class="forgot-password-container">
        <span @click="router.push('/forgot-password')" class="link-orange">
        Mot de passe oublié ?
        </span>
        </div>
      </div>
      
      <button type="submit" class="btn-connexion":disabled="loading">Connexion</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../services/api'
import { useRouter } from 'vue-router' // Pour rediriger l'utilisateur après

// On crée deux variables "réactives" pour stocker ce que l'utilisateur tape
const pseudo = ref('')
const password = ref('')
const router = useRouter()
const loading = ref(false)

// On crée une fonction qui sera appelée quand on clique sur le bouton
const login = async () => {
  if (!pseudo.value || !password.value) return alert("Remplis tous les champs !");
  loading.value = true
  try {
    await api.get('/sanctum/csrf-cookie')
    const response = await api.post('/api/login', {
      pseudo: pseudo.value,
      password: password.value
    });
    // Mise à jour du stockage local pour le Front-end
    // On n'utilise plus de 'user-token' car c'est géré par cookie
    localStorage.setItem('is_logged', 'true')
    // On récupère le pseudo renvoyé par le PHP
    const userPseudo = response.data.user?.pseudo || pseudo.value
    localStorage.setItem('user-pseudo', userPseudo)
      router.push('/garage'); 
  } catch (error) {
    console.error("Erreur de connexion :", error)
    const message = error.response?.data?.message || "Identifiants incorrects ou serveur injoignable."
    alert(message)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.hero-container {
  display: flex;
  justify-content: space-evenly; 
  align-items: flex-start;
  padding: 0 5%;
  flex-grow: 1; /* Prend tout l'espace central entre la nav et le footer */
  }

/* Style commun aux deux blocs (le fond noir transparent) */
.hero-text, .hero-form {
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(5px);      /* Floute légèrement l'image derrière (très moderne) */
  padding: 30px;
  border-radius: 50px;            
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.1); 
}

.hero-text {
  max-width: 460px;
  text-align: center;
}

.highlight {
  color: #FF6B35;
}

.hero-form {
  width: 320px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  align-items: center; 
  padding: 40px 30px;
  box-sizing: border-box;
  margin-left: 20px;
  margin-top: 50px;
}

input {
  width: 100%;
  padding: 12px;
  border-radius: 25px;
  border: none;
  margin-top: 5px;
  box-sizing: border-box;
}

.btn-connexion {
  background-color: #FF6B35;
  color: #1A1A1A;
  border: none;
  padding: 15px;
  border-radius: 30px;
  font-weight: bold;
  cursor: pointer;
  margin-top: 10px;
  width: 180px;
  transition: all 0.3s ease;
}

.btn-connexion:hover {
  background-color: white;
  transform: translateY(-2px);
  color:#FF6B35;
}

.forgot-password-container {
  width: 100%;
  text-align: right; 
  margin-top: 8px;
}

.link-orange {
  color: #FF6B35;
  font-size: 0.85rem;
  text-decoration: underline; /* Le soulignement  */
  cursor: pointer;
  transition: opacity 0.2s;
}

.link-orange:hover {
  opacity: 0.8; 
}

@media (max-width: 768px) {
  .hero-container {
    flex-direction: column;
    align-items: center;    
    padding-top: 5rem;      
    gap: 20px;             
  }

  .hero-text, .hero-form {
    max-width: 90%;        
    margin-left: 0;       
    margin-top: 0;
    padding: 30px;       
  }

  h1 {
    font-size: 1.5rem;   
  }
}
</style>