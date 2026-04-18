<template>
  <div class="reset-password-wrapper">
    <div class="reset-card">
      <h1>Choisir un nouveau mot de passe</h1>
      
      <form @submit.prevent="handleReset">
        <div class="input-group">
          <label>Nouveau mot de passe :</label>
          <input type="password" v-model="form.password" required minlength="8" placeholder="Minimum 8 caractères">
        </div>

        <div class="input-group">
          <label>Confirmez le mot de passe :</label>
          <input type="password" v-model="form.password_confirmation" required placeholder="Répétez le mot de passe">
        </div>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Mise à jour...' : 'Mettre à jour le mot de passe' }}
        </button>
      </form>

      <p v-if="message" class="message-info">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const message = ref('');

const form = reactive({
  token: route.params.token, // Le token est récupéré dans l'URL
  email: route.query.email,  // On récupérera l'email aussi dans l'URL
  password: '',
  password_confirmation: ''
});

const handleReset = async () => {
  loading.value = true;
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/reset-password', form);
    message.value = "Mot de passe modifié avec succès !";
    setTimeout(() => router.push('/'), 1000);
  } catch (error) {
    message.value = error.response?.data?.message || "Une erreur est survenue.";
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.reset-password-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 25%;
  /* Assure-toi que le nom de l'image est le bon */
  background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('@/assets/ton-image.jpg') no-repeat center center fixed;
  background-size: cover;
}

.reset-card {
  background: rgba(0, 0, 0, 0.95);
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
  width: 90%;
  max-width: 400px;
  text-align: center;
}

h1 {
  color: white;
  margin-bottom: 2rem;
  font-size: 1.4rem;
  font-weight: bold;
  text-transform: uppercase;
}

.input-group {
  margin-bottom: 1.2rem;
  text-align: left;
}

.input-group label {
  display: block;
  margin-bottom: 0.4rem;
  font-weight: 600;
  color: white;
  font-size: 0.9rem;
}

input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-sizing: border-box;
  font-size: 1rem;
}

input:focus {
  border: solid 4px #ff7e41;
  outline: none;
  box-shadow: 0 0 12px rgba(255, 126, 65, 0.4);
  background: rgba(255, 255, 255, 0.9);
}

button {
  width: 100%;
  padding: 14px;
  background-color: #FF6B35;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  font-size: 1rem;
  text-transform: uppercase;
  transition: all 0.2s ease;
  margin-top: 1rem;
}

button:hover:not(:disabled) {
  background-color: #c0392b;
  transform: translateY(-1px);
}

button:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.message-info {
  margin-top: 1.5rem;
  font-weight: bold;
  color: #2c3e50;
}
</style>