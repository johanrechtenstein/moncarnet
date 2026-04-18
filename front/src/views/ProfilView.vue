<template>
  <div class="maintenance-page-wrapper">
    <div class="maintenance-container profile-card">
      <h2 class="profile-title">Mon Profil</h2>
      
      <div class="profile-info">
        <div class="info-group">
          <label>Pseudo</label>
          <div class="info-value">{{ user.pseudo }}</div>
        </div>
        
        <div class="info-group">
  <div class="label-header">
    <label>E-mail</label>
    <button @click="isEditingEmail = !isEditingEmail" class="btn-edit-small">
      {{ isEditingEmail ? 'annuler' : 'modifier' }}
    </button>
  </div>

  <div v-if="!isEditingEmail" class="info-value">{{ user.email }}</div>

  <div v-else class="edit-input-wrapper">
    <input type="email" v-model="emailForm.email" class="info-value-input">
    <button @click="updateEmail" class="btn-save-small">OK</button>
  </div>
</div>
      </div>

      <div class="actions-section">
        <button class="btn-password" @click="requestPasswordReset">
          Modifier mon mot de passe 
        </button>
        
        <button class="btn-delete" @click="confirmDeleteAccount">
          Supprimer mon compte
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref({ pseudo: '', email: '' })
const isEditingEmail = ref(false);
const emailForm = ref({ email: '' });






// Charger les infos de l'utilisateur connecté
const fetchUserProfile = async () => {
  try {
    const token = localStorage.getItem('user-token')
    const res = await axios.get('http://127.0.0.1:8000/api/user', {
      headers: { Authorization: `Bearer ${token}` }
    })
    user.value = res.data
  } catch (e) {
    console.error("Erreur profil", e)
  }
}

// Fonction pour supprimer le compte
const confirmDeleteAccount = async () => {
  const password = prompt("Pour confirmer la suppression de votre compte, veuillez saisir votre mot de passe :");
  
  if (password) {
    try {
      const token = localStorage.getItem('user-token')
      await axios.delete('http://127.0.0.1:8000/api/user', {
      // Le 2ème argument contient TOUT
      headers: { 
      Authorization: `Bearer ${token}` 
      },
      data: { 
      password: password // On met le body ici pour un DELETE
      }
      })

      localStorage.removeItem('user-token')
      // Optionnel : localStorage.removeItem('user-pseudo')
      router.push('/')
      alert("Compte supprimé avec succès.")
      } catch (e) {
      // On récupère le message d'erreur de Laravel (ex: mot de passe incorrect)
      const errorMsg = e.response?.data?.errors?.password?.[0] || "Erreur lors de la suppression.";
      alert(errorMsg);
    }
  }
}

const requestPasswordReset = async () => {
  try {
    const token = localStorage.getItem('user-token');
    
    // On appelle ton API Laravel
    const response = await axios.post('http://127.0.0.1:8000/api/password/email', 
      { email: user.value.email },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    
    alert(response.data.message);
  } catch (e) {
    console.error(e);
    alert("Erreur lors de la demande de réinitialisation.");
  }
};

onMounted(fetchUserProfile)


// Quand on ouvre la modif, on remplit avec l'actuel
watch(isEditingEmail, (val) => {
  if(val) emailForm.value.email = user.value.email;
});

const updateEmail = async () => {
  try {
    const token = localStorage.getItem('user-token');
    await axios.put('http://127.0.0.1:8000/api/user', emailForm.value, {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    user.value.email = emailForm.value.email;
    isEditingEmail.value = false;
    alert("Email mis à jour !");
  } catch (e) {
    alert(e.response?.data?.message || "Erreur lors du changement");
  }
};


</script>


<style scoped>
.profile-card {
  max-width: 500px;
  margin: 100px auto; /* Pour le centrer un peu plus bas */
  padding: 40px;
  background-color: #1a1a1a; /* Fond noir solide comme ta table */
  border: 2px solid #FF6B35; /* Ta bordure verte */
  box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.profile-title {
  color: white;
  margin-bottom: 30px;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.info-group {
  margin-bottom: 25px;
  text-align: left;
}

.info-group label {
  color: #FF6B35;
  font-size: 0.8em;
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
  margin-left: 5px;
}

.info-value {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  padding: 12px 20px;
  border-radius: 15px;
  border: 1px solid #444;
  font-size: 1.1em;
}

.actions-section {
  margin-top: 40px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.btn-password {
  background: #333;
  color: white;
  border: 1px solid #555;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
  transition: 0.3s;
}

.btn-password:hover {
  background: #444;
}

.btn-delete {
  background: transparent;
  color: #ff4d4d;
  border: 1px solid #ff4d4d;
  padding: 10px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 0.9em;
}

.btn-delete:hover {
  background: #ff4d4d;
  color: white;
}

.label-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 5px;
}

.btn-edit-small {
  background: transparent;
  border: none;
  color: #FF6B35;
  cursor: pointer;
  font-size: 0.8em;
  text-decoration: underline;
  opacity: 0.7;
}

.btn-edit-small:hover {
  opacity: 1;
}

.edit-input-wrapper {
  display: flex;
  gap: 10px;
}

.info-value-input {
  flex: 1;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 10px 15px;
  border-radius: 10px;
  border: 1px solid #2ecc71;
  outline: none;
}

.btn-save-small {
  background: #2ecc71;
  color: #1a1a1a;
  border: none;
  padding: 0 15px;
  border-radius: 10px;
  font-weight: bold;
  cursor: pointer;
}
</style>