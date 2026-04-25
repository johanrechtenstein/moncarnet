<template>
  <div class="hero-container">
    <div class="hero-form">
      <h1>Récupération de mot de passe</h1>
      <p class="instruction">
        Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.
      </p>

      <div class="form-group">
        <label>E-mail :</label>
        <input 
          v-model="email" 
          type="email" 
          placeholder="votre@email.com" 
          :disabled="loading"
        />
      </div>

      <button 
        @click="sendResetLink" 
        class="btn-connexion" 
        :disabled="loading"
      >
        {{ loading ? 'Envoi en cours...' : 'Envoyer le lien' }}
      </button>

      <p v-if="message" :class="['message', messageType]">{{ message }}</p>

      <span @click="router.push('/')" class="link-orange back-link">
        Retour à la connexion
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../services/api'
import { useRouter } from 'vue-router'

const email = ref('')
const loading = ref(false)
const message = ref('')
const messageType = ref('') // 'success' ou 'error'
const router = useRouter()

const sendResetLink = async () => {
  if (!email.value) {
    message.value = "Veuillez entrer votre adresse e-mail."
    messageType.value = "error"
    return
  }

  loading.value = true
  message.value = ""

  try {
    await api.get('/sanctum/csrf-cookie');
    const response = await api.post('/api/forgot-password', {
      email: email.value
    });
    message.value = response.data.message;
    messageType.value = "success";
  } catch (error) {
    console.error(error);
    messageType.value = "error";
    message.value = error.response?.data?.message || "Une erreur est survenue.";
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
/* On réutilise tes styles pour garder la cohérence */
.hero-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 75vh;
}

.hero-form {
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(5px);
  padding: 40px 30px;
  border-radius: 50px;
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.1);
  width: auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
  align-items: center;
  text-align: center;
}

h1 {
  color: #FFffff;
  margin: 0;
}

.instruction {
  font-size: 0.9rem;
  opacity: 0.8;
  line-height: 1.4;
}

.form-group {
  width: 100%;
  text-align: left;
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
  width: 100%;
  transition: all 0.3s ease;
}

.btn-connexion:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.link-orange {
  color: #FF6B35;
  font-size: 0.85rem;
  text-decoration: underline;
  cursor: pointer;
}

.back-link {
  margin-top: 10px;
}

.message {
  font-size: 0.85rem;
  padding: 10px;
  border-radius: 10px;
}

.success {
  color: #4ade80; /* Vert */
}

.error {
  color: #f87171; /* Rouge */
}
</style>