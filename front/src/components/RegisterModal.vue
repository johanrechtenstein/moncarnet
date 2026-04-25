<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="close">
    <div class="modal-card">
      <button class="close-btn" @click="close">×</button>
      <h2>Créer un compte</h2>
      
      <form @submit.prevent="handleRegister">
        <div class="input-group">
          <label>Pseudo</label>
          <input type="text" v-model="form.pseudo" @blur="verifyPseudo" placeholder="Ton pseudo">
          <span v-if="pseudoStatus === 'taken'" class="error-text">Ce pseudo est déjà utilisé.</span>
          <span v-if="pseudoStatus === 'available'" class="success-text">Pseudo disponible !</span>
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" v-model="form.email" @blur="verifyEmail" placeholder="exemple@mail.com">
          <span v-if="emailStatus === 'taken'" class="error-text">Cet email est déjà inscrit.</span>
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
import api from '../services/api'
import { useRouter } from 'vue-router'

const props = defineProps(['isOpen'])
const emit = defineEmits(['close'])
const router = useRouter()
const loading = ref(false)
const emailStatus = ref('') // 'taken' ou 'available'
const pseudoStatus = ref('')

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
  pseudoStatus.value = ''
  emailStatus.value = ''
}

const verifyPseudo = async () => {
    if (form.pseudo.length < 3) return
    try {
        const response = await api.post('/api/check-pseudo', { pseudo: form.pseudo })
        pseudoStatus.value = response.data.exists ? 'taken' : 'available'
    } catch (e) { console.error(e) }
}

const verifyEmail = async () => {
    if (!form.email.includes('@')) return
    try {
        const response = await api.post('/api/check-email', { email: form.email })
        emailStatus.value = response.data.exists ? 'taken' : 'available'
    } catch (e) { console.error(e) }
}

const handleRegister = async () => {
  loading.value = true
  try {
    await api.get('/sanctum/csrf-cookie')
    const response = await api.post('/api/register', form)
    
    // On stocke le token car ton backend le renvoie direct !
    localStorage.setItem('is_logged', 'true')
    localStorage.setItem('user-pseudo', response.data.user.pseudo)
    resetForm()
    close()
    router.push('/garage')
 } catch (e) {
    if (e.response?.data?.errors) {
        // Au lieu d'un message générique, on prend la première erreur de validation
        // Exemple : "L'adresse email est déjà utilisée."
        const firstError = Object.values(e.response.data.errors)[0][0];
        alert(firstError);
    } else {
        alert(e.response?.data?.message || "Erreur lors de l'inscription");
    }
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


.error-text {
  color: #ff4444;
  font-size: 0.8rem;
  margin-top: 4px;
  display: block;
}

.success-text {
  color: #00c851;
  font-size: 0.8rem;
  margin-top: 4px;
  display: block;
}

</style>