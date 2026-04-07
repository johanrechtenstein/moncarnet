<template>
  <div class="hero-container">
    <div class="hero-text">
      <h1>Votre passion mérite le <span class="highlight">digital</span></h1>
      <p>"Suivez l'<span class="highlight">entretien</span> de votre véhicule en temps réel, car son <span class="highlight">passé</span> vous le rendra dans le <span class="highlight">futur</span>."</p>
    </div>

    <div class="hero-form">
      <div class="form-group">
        <label>Pseudo :</label>
        <input v-model="pseudo" type="text" placeholder="Pseudo" />
      </div>
      <div class="form-group">
        <label>Mot de passe :</label>
        <input v-model="password" type="password" placeholder="••••••••" />
      </div>
      <button @click="login" class="btn-connexion">Connexion</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router' // Pour rediriger l'utilisateur après

// On crée deux variables "réactives" pour stocker ce que l'utilisateur tape
const pseudo = ref('')
const password = ref('')
const router = useRouter()

// On crée une fonction qui sera appelée quand on clique sur le bouton
const login = async () => {
  try {
    // On envoie les données à l'API
    // Remplace l'URL par celle de ton backend plus tard
    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      pseudo: pseudo.value,
      password: password.value
    });
console.log("Voici ce que le PHP a envoyé :", response.data);
    // Si l'API répond avec succès
    const token = response.data.access_token || response.data.token;
    if (token) {
      // On stocke le jeton de sécurité (Token) dans le navigateur
      localStorage.setItem('user-token', token);
      localStorage.setItem('user-pseudo', pseudo.value);   
      // On redirige vers le garage (le dashboard)
      router.push('/garage'); 
    }else {
      // Au cas où l'API répond 200 mais sans token (peu probable mais utile pour débugger)
      console.log("Réponse reçue mais pas de token :", response.data);
    }
  } catch (error) {
    console.error("Erreur de connexion :", error);
    alert("Identifiants incorrects ou serveur injoignable.");
  }
}
</script>

<style scoped>
.hero-container {
  display: flex;
  justify-content: space-evenly; /* Espace entre les deux blocs */
  /*align-items: center;*/
  align-items: flex-start;
  padding: 0 5%;
  flex-grow: 1; /* Prend tout l'espace central entre la nav et le footer */
  
}

/* Style commun aux deux blocs (le fond noir transparent) */
.hero-text, .hero-form {
  background: rgba(0, 0, 0, 0.75); /* Noir bien opaque */
  backdrop-filter: blur(5px);      /* Floute légèrement l'image derrière (très moderne) */
  padding: 30px;
  border-radius: 50px;             /* Bords très arrondis comme sur ta maquette */
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.1); /* Petit liseré discret */
}

.hero-text {
  max-width: 460px;
  text-align: center;
}

.highlight {
  color: #FF6B35;
}

.hero-form {
  width: 320px; /* On stabilise la largeur de la capsule */
  display: flex;
  flex-direction: column;
  gap: 20px;
  align-items: center; /* Centre le bouton Connexion */
  padding: 40px 30px;
  box-sizing: border-box;
  margin-left: 20px;
  margin-top: 50px;
}

input {
  width: 100%;
  padding: 12px;
  border-radius: 25px; /* Inputs arrondis aussi */
  border: none;
  margin-top: 5px;
  box-sizing: border-box;
  
}

.btn-connexion {
  background-color: #FF6B35;
  color: white;
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
  background-color: white; /* Un orange un peu plus sombre au survol */
  transform: translateY(-2px); /* Un léger mouvement vers le haut */
  color:#FF6B35;
}




/* --- LE BLOC MAGIQUE POUR MOBILE --- */
@media (max-width: 768px) {
  .hero-container {
    flex-direction: column; /* On empile au lieu de mettre côte à côte */
    align-items: center;    /* On centre tout le monde */
    padding-top: 5rem;      /* On remonte un peu le tout */
    gap: 20px;              /* On laisse respirer entre les deux boîtes */
  }

  .hero-text, .hero-form {
    max-width: 90%;        /* Les boîtes prennent presque toute la largeur */
    margin-left: 0;        /* On enlève tes décalages de "margin" */
    margin-top: 0;
    padding: 30px;         /* On réduit un peu le padding interne */
  }

  h1 {
    font-size: 1.5rem;     /* On réduit la taille du titre */
  }
}
</style>