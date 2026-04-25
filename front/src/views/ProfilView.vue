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
    <button @click="isEditingEmail ? isEditingEmail = false : startEditingEmail()" class="btn-edit-small">
      {{ isEditingEmail ? 'annuler' : 'modifier mon email' }}
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
import api from '../services/api'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref({ pseudo: '', email: '' })
const isEditingEmail = ref(false);
const emailForm = ref({ email: '' });


// Charger les infos de l'utilisateur connecté
const fetchUserProfile = async () => {
  try {
    const res = await api.get('/api/user')
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
      await api.delete('/api/user', {
      data: { 
      password: password // On met le body ici pour un DELETE
      }
      })
      localStorage.removeItem('user-token')
      // Optionnel : localStorage.removeItem('user-pseudo')
      router.push('/')
      alert("Compte supprimé avec succès.")
      } catch (e) {
      const errorMsg = e.response?.data?.errors?.password?.[0] || "Erreur lors de la suppression.";
      alert(errorMsg);
    }
  }
}
//lien pour changement de mdp
const requestPasswordReset = async () => {
  try {
    // On appelle ton API Laravel
    await api.get('/sanctum/csrf-cookie')
    const response = await api.post('/api/password/email', {
      email: user.value.email 
    });
    alert(response.data.message);
  } catch (e) {
    console.error(e);
    alert("Erreur lors de la demande de réinitialisation.");
  }
};

onMounted(fetchUserProfile)
// Plus simple qu'un watch : on pré-remplit ici
const startEditingEmail = () => {
  emailForm.value.email = user.value.email
  isEditingEmail.value = true
}

const updateEmail = async () => {
  try {
    await api.put('/api/user', emailForm.value);
    user.value.email = emailForm.value.email;
    isEditingEmail.value = false;
    alert("Email mis à jour !");
  } catch (e) {
    // Laravel renvoie souvent les erreurs de validation dans .errors
    const msg = e.response?.data?.errors?.email?.[0] || e.response?.data?.message || "Erreur";
    alert(msg);
  }
};
</script>


<style scoped>

.maintenance-page-wrapper{
   background-color: rgba(0, 0, 0, 0.9); /* Fond noir opaque */
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  padding: 30px;
  border-radius: 15px;
}


.profile-card {
  max-width: 500px;
  margin:  auto; 
  padding: 30px;
  background-color: rgba(0, 0, 0, 0.9); 
  border: 2px solid white;
  box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  border-radius: 15px;
}

.profile-title {
  color: white;
  margin-bottom: 40px;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.info-group {
  margin-bottom: 25px;
  text-align: left;
}

.info-group label {
  color: white;
  font-size: 0.8em;
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
  margin-left: 5px;
}

.info-value {
  background: rgba(255, 255, 255, 0.2);
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



@media (max-width: 440px) {
.profile-card{
  padding: 15px;
}
}



</style>