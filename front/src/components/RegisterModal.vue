<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="close">
    <div class="modal-card">
      <button class="close-btn" @click="close">×</button>
      <h2>Créer un compte</h2>
      
      <form @submit.prevent="handleRegister">
        <div class="input-group">
          <label>Pseudo</label>
          <input type="text" v-model="form.pseudo" required placeholder="Ton pseudo">
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" v-model="form.email" required placeholder="exemple@mail.com">
        </div>

        <div class="input-group">
          <label>Mot de passe</label>
          <input type="password" v-model="form.password" required minlength="8" placeholder="••••••••">
        </div>

        <div class="input-group">
          <label>Confirmation</label>
          <input type="password" v-model="form.password_confirmation" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-register" :disabled="loading">
          {{ loading ? 'Chargement...' : 'Rejoindre le garage' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const props = defineProps(['isOpen'])
const emit = defineEmits(['close'])
const router = useRouter()
const loading = ref(false)

const form = reactive({
  pseudo: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const close = () => {
  resetForm()
  emit('close')
}

// La fonction qui remet tout à zéro
const resetForm = () => {
  form.pseudo = ''
  form.email = ''
  form.password = ''
  form.password_confirmation = ''
}

const handleRegister = async () => {
  loading.value = true
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/register', form)
    
    // On stocke le token car ton backend le renvoie direct !
    localStorage.setItem('user-token', response.data.access_token)
    localStorage.setItem('user-pseudo', response.data.user.pseudo)
    resetForm()
    close()
    router.push('/garage')
  } catch (e) {
    alert(e.response?.data?.message || "Erreur lors de l'inscription")
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(5px);
  display: flex; justify-content: center; align-items: center;
  z-index: 2000;
}

.modal-card {
  background: #111;
  border: 2px solid #FF6B35;
  padding: 30px;
  border-radius: 20px;
  width: 90%;
  max-width: 400px;
  position: relative;
  color: white;
}

.close-btn {
  position: absolute; top: 15px; right: 20px;
  background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;
}

/* On réutilise tes styles d'inputs ici pour la cohérence */
input {
  width: 100%; padding: 10px; margin-top: 5px;
  border-radius: 8px; border: 1px solid #444;
  background: #222; color: white;
}

input:focus {
  border-color: #FF6B35; outline: none;
}

.btn-register {
  width: 100%; padding: 12px; margin-top: 20px;
  background: #FF6B35; color: white; border: none;
  border-radius: 25px; font-weight: bold; cursor: pointer;
}
</style>