<template>
  <div class="contact-page">
    <div class="contact-container">
      <div class="border-card">
      <div class="contact-card form-card">
        <h2>Contact</h2>
        <form @submit.prevent="handleSubmit">
          <div class="input-group">
            <label>Mail de réponse</label>
            <input type="email" placeholder="Ton email..." v-model="email" required>
          </div>
          
          <div class="input-group">
            <label>Message</label>
            <textarea placeholder="Ton message..." v-model="message" required></textarea>
          </div>
          <p style="font-size: 0.8rem; opacity: 0.7; margin: 15px 0;">
          En envoyant ce message, vous acceptez que vos données soient traitées conformément à nos 
          <router-link to="/mentions" style="color: #FF6B35; text-decoration: underline;">mentions légales</router-link>.
          </p>
          <button type="submit" class="btn-send">Envoyer</button>
        </form>
      </div>
      </div>

      <div class="border-card right">
      <div class="contact-card text-card">
        <h2>Le point de départ</h2>
        <p>
          Passionné par la mécanique, mon problème le plus courant était d'avoir le carnet d'entretien sous la main pour le mettre à jours. 
          Le format papier est falsifiable, salissable et ne garantie pas un entretien suivi. 
        </p>
        <h2>Ma solution</h2>
        <p>
          J'ai donc créé cette application afin d'y avoir accès en temps réel et apporter un argument fort au moment de la revente. 
        </p>
      </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const message = ref('')

const handleSubmit = async () => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
  if (!emailPattern.test(email.value)) {
    alert("Oups ! L'adresse mail n'est pas valide.");
    return; // On arrête tout ici
  }

  if (message.value.length < 10) {
    alert("Le message est un peu court, non ? (10 caractères min.)");
    return;
  }
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/contact', {
      email: email.value,
      message: message.value
    });
    alert("C'est parti ! Message envoyé.");
    email.value = '';
    message.value = '';
  } catch (e) {
    alert("Erreur : " + (e.response?.data?.message || "Vérifie tes champs"));
  }
}
</script>


<style scoped>
.contact-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 75vh;
  width: 100%;
}

.contact-container {
  display: flex;
  gap: 100px;
  max-width: 1100px;
  width: 95%; 
  align-items: flex-start;
  }

.border-card {
  flex: 1;              
  padding: 15px;
  border-radius: 15px;
  background-color: rgba(0, 0, 0, 0.9); /* Fond noir opaque */
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}



.right{ margin-top: 50px;
}


.contact-card {
  border: 4px solid white; /* Grosse bordure blanche comme sur ton dessin */
  border-radius: 15px; /* Bords très arrondis */
  padding: 20px;
  color: white;
  flex-direction: column;
  box-sizing: border-box;
}

/* --- SECTION FORMULAIRE --- */
.form-card h2 {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 15px;
  
}

.input-group {
  margin-bottom: 20px;
}

.input-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
}

.input-group input, 
.input-group textarea {
  width: 90%;
  padding: 15px;
  border-radius: 15px;
  border: none;
  background-color: white; /* Fond blanc pour les champs */
  color: #333;
  font-size: 1rem;
}

.input-group textarea {
  height: 100px;
  resize: none; /* Empêche de déformer la carte */
}

.btn-send {
  display: block;
  width: 60%;
  margin: 20px auto 0;
  background-color: #FF6B35; /* Ton orange signature */
  color: white;
  border: none;
  padding: 12px;
  border-radius: 25px;
  font-weight: bold;
  font-size: 1.2rem;
  cursor: pointer;
  transition: transform 0.2s, background-color 0.2s;
}

.btn-send:hover {
  background-color: white;
  color: #FF6B35;
  transform: scale(1.05);
}

/* --- SECTION TEXTE --- */
.text-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  line-height: 1.6;
  font-size: 1.1rem;
  text-align: center;
}

/* Responsive : On empile les cartes sur mobile */
@media (max-width: 900px) {
  .contact-container {
    flex-direction: column;
    gap: 30px; 
    align-items: center; 
    width: 100%; /* S'assure que le container prend toute la place */
  }

  /* C'est ICI qu'il faut forcer la largeur sur le bloc NOIR */
  .border-card {
    width: 90%;      /* Pour garder une petite marge sur les bords de l'écran */
    max-width: 500px; /* Mais pas trop large non plus */
    margin-top: 0 !important; /* On annule ton décalage asymétrique sur mobile */
  }

  .contact-card {
    width: 100%; /* Le cadre blanc remplit alors tout le cadre noir */
  }
}
</style>