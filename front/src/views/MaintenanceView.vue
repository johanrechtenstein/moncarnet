<template>
  <div v-if="loading" class="loader-container">
    <div class="loader">Chargement du garage...</div>
  </div>

  <div v-else class="maintenance-page-wrapper">
    <div class="maintenance-container" :class="containerUrgencyClass">
      
      <div class="maintenance-header">
        <div class="car-info-badge">
          <img src="../assets/voiture1.png" class="mini-car-img">
          <div class="plate-number">{{ car.immatriculation }}</div>
          <div class="car-model">{{ car.marque }} | {{ car.modele }}</div>
          <button class="close-btn" @click="$router.push('/garage')">retour</button>
        </div>
      </div>

      <div class="table-container">
        <table class="maintenance-table">
          <thead>
            <tr>
              <th></th>
              <th>catégorie</th>
              <th>date</th>
              <th>kilométrage</th>
              <th>prochain entretien</th><th>description</th>
              <th>facture</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in filteredLogs" :key="log.id">
              <td>
  <div class="action-cell">
    <button 
      v-if="getEcheanceStatus(log) !== 'ok' && log.status !== 'validated'" 
      @click="validateMaintenance(log)"
      class="btn-small-validate"
      title="Marquer comme effectué"
    >
      à faire
    </button>

    <span v-else class="edit-icon" style="cursor:pointer">🟢</span>
  </div>
</td>
              <td>{{ log.categorie?.nom || 'N/A' }}</td>
              <td>{{ new Date(log.date).toLocaleDateString() }}</td>
              <td>{{ log.kilometrage }} km</td>
              <td>
                  <div v-if="log.echeance_km" class="echeance-badge km"  :class="getEcheanceStatus(log)">
                  {{ log.echeance_km }} km
                  </div>
                  <div v-if="log.echeance_date" class="echeance-badge date"  :class="getEcheanceStatus(log)">
                  {{ new Date(log.echeance_date).toLocaleDateString() }}
                  </div>
                  <span v-if="!log.echeance_km && !log.echeance_date">-</span>
              </td>
              <td class="description-cell">{{ log.description }}</td>
              <td>{{ log.facture_url || '-' }}</td>
              <td>
                <span 
                v-if="isEditable(log.created_at)" 
                class="edit-icon" 
                style="cursor:pointer"
                @click="openEditForm(log)"
                >
                ✏️
                </span>
  
                <span v-else style="opacity: 0.3; cursor: not-allowed;" title="Délai de modification dépassé">
                🔒
                </span>
                </td>
            </tr>
            <tr v-if="maintenanceLogs.length === 0">
              <td colspan="6" style="padding: 20px; color: gray;">Aucun historique pour ce véhicule.</td>
            </tr>
          </tbody>
        </table>
        
        <div class="table-footer">
        
  <div class="footer-controls">
    <label><input type="checkbox" v-model="hideReleves"> cacher les relevés</label>
    
    <button class="btn-add-log" @click="resetFormAndOpen()">
      {{ showAddForm ? 'Annuler' : 'Ajouter' }}
    </button>
  </div>

  <div v-if="showAddForm" class="add-form-overlay">
    <div class="inline-form">
      <select v-model="form.categorie_id">
        <option value="" disabled>Catégorie</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.nom }}
        </option>
      </select>
      
      <input type="date" v-model="form.date">
      <input type="number" v-model="form.kilometrage" placeholder="Km">
      <input type="text" v-model="form.description" placeholder="Description...">
      <input type="number" v-model="form.echeance_km" placeholder="Échéance (km)">
      <input type="date" v-model="form.echeance_date" title="Date d'échéance">
      
      <button class="btn-confirm" @click="isEditing ? updateMaintenance() : addMaintenance()">
      {{ isEditing ? 'Mettre à jour' : 'Enregistrer' }}
      </button>
    </div>
  </div>
</div>
     
        </div>
      </div>

      
    </div>
  
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useMaintenance } from '../composables/useMaintenance.js'


const route = useRoute()
const car = ref({})
const maintenanceLogs = ref([])
const categories = ref([])
const loading = ref(true)
const showAddForm = ref(false) 
const hideReleves = ref(false)
const isEditing = ref(false)
const currentEditId = ref(null)




// échéance de la voiture
const { getEcheanceStatus: calculateStatus, getVehicleStatus } = useMaintenance()
// Ta computed devient super simple :
const containerUrgencyClass = computed(() => {
  return getVehicleStatus(maintenanceLogs.value, car.value.kilometrage)
})

// On crée une petite fonction locale qui fait le pont avec ta variable réactive
const getEcheanceStatus = (log) => {
  return calculateStatus(log, kilometrageActuel.value)
}

//kilometrage actuel
const kilometrageActuel = computed(() => {
  if (maintenanceLogs.value.length === 0) return car.value.kilometrage;

  // On extrait tous les kilométrages des logs, on ajoute celui de base, 
  // et on prend le maximum.
  const kms = maintenanceLogs.value.map(log => log.kilometrage);
  return Math.max(...kms, car.value.kilometrage || 0);
});


//fonction pour désactiver l'alerte
const validateMaintenance = async (log) => {
  try {
    const token = localStorage.getItem('user-token');
    
    // On envoie UNIQUEMENT le statut pour déclencher le "Cas Particulier" du contrôleur
    const res = await axios.put(`http://127.0.0.1:8000/api/maintenances/${log.id}`, 
      { status: 'validated' }, 
      { headers: { Authorization: `Bearer ${token}` } }
    );

    // Si l'API répond OK, on met à jour l'objet localement pour éteindre le rouge
    log.status = 'validated';
    
    console.log("Maintenance validée avec succès");
  } catch (e) {
    console.error("Erreur lors de la validation :", e.response?.data || e.message);
    alert("Erreur lors de la mise à jour du statut.");
  }
};



// fitrage des données pour ne plus avoir les relevés.
const filteredLogs = computed(() => {
  if (hideReleves.value) {
    // On garde tout SAUF ceux dont la catégorie est "Relevé" (ID 1 par exemple)
    // Adapte l'ID ou le nom selon ta base de données
    return maintenanceLogs.value.filter(log => log.categorie?.nom !== 'Relevé')
  }
  return maintenanceLogs.value
})

// Objet pour le formulaire d'ajout
const form = ref({
  date: new Date().toISOString().split('T')[0], // Aujourd'hui par défaut
  kilometrage: '',
  description: '',
  echeance_km: '',
  echeance_date: '',
  facture_url: null,
  categorie_id: '',
  car_id: route.params.id
})

// 1. Charger les catégories pour le menu déroulant
const fetchCategories = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/categories')
    categories.value = res.data
    console.log("Catégories récupérées :", categories.value)
  } catch (e) { console.error("Erreur catégories", e) }
}

// 2. Envoyer la nouvelle maintenance
const addMaintenance = async () => {
  // Sécurité : le kilométrage saisi doit être >= au kilométrage actuel
  if (form.value.kilometrage < kilometrageActuel.value) {
    alert(`Erreur : Le kilométrage saisi (${form.value.kilometrage}) est inférieur au compteur actuel (${car.value.kilometrage}).`);
    return; // On arrête tout
  }
  // 2. Sécurité : L'échéance future (ex: 215 000)
  // On ne vérifie que si l'utilisateur a rempli le champ echeance_km
  if (form.value.echeance_km && form.value.echeance_km <= form.value.kilometrage) {
    alert(`Erreur : L'échéance (${form.value.echeance_km} km) doit être supérieure au kilométrage de l'intervention actuelle (${form.value.kilometrage} km).`);
    return;
  }
  try {
    const token = localStorage.getItem('user-token')
    const res = await axios.post('http://127.0.0.1:8000/api/maintenances', form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    // On ajoute le résultat (qui contient .categorie grâce au .load()) au tableau
    maintenanceLogs.value.unshift(res.data)
    
    // Reset du formulaire
    form.value.description = ''
    form.value.kilometrage = ''
    form.value.echeance_km = ''  
    form.value.echeance_date = ''
    showAddForm.value = false
  } catch (e) {
    console.error("Erreur ajout", e.response?.data)
  }
}


const fetchMaintenanceData = async () => {
  try {
    const token = localStorage.getItem('user-token')
    const carId = route.params.id

    const res = await axios.get(`http://127.0.0.1:8000/api/cars/${carId}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    // On extrait l'objet voiture
    const carData = res.data.data
    
    if (carData) {
      car.value = carData
      // ON UTILISE carData ICI AUSSI !
      maintenanceLogs.value = carData.maintenances || []
      
      console.log("Données de la voiture chargées :", carData)
    } else {
      console.warn("Aucune donnée trouvée pour cette voiture.")
    }

  } catch (error) {
    console.error("Erreur de chargement :", error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchMaintenanceData() 
  fetchCategories()
})

// -------------------------------------------------------------------------------------------------------------------------
//put maintenance

const isEditable = (createdAt) => {
  if (!createdAt) return false;
  const diffInHours = (new Date() - new Date(createdAt)) / (1000 * 60 * 60);
  return diffInHours < 24;
};

const openEditForm = (log) => {
  isEditing.value = true
  currentEditId.value = log.id
  
  // On remplit le formulaire avec les données de la ligne cliquée
  form.value = {date: log.date ? log.date.split('T')[0] : '', // Format YYYY-MM-DD pour l'input date
    kilometrage: log.kilometrage,
    description: log.description,
    echeance_km: log.echeance_km,
    echeance_date: log.echeance_date ? log.echeance_date.split('T')[0] : '',
    categorie_id: log.categorie_id || log.categorie?.id, // Très important pour le <select>
    car_id: route.params.id
  }
  
  showAddForm.value = true // On ouvre le formulaire
}

const updateMaintenance = async () => {
  const token = localStorage.getItem('user-token')
  
  try {
    const res = await axios.put(`http://127.0.0.1:8000/api/maintenances/${currentEditId.value}`, form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })

    // Ton contrôleur PHP renvoie soit 'data' (moins de 24h) soit 'new_entry' (plus de 24h)
    // Pour ne pas s'embêter, on rafraîchit simplement les données :
 // On recharge tout pour voir les changements (et l'historique si > 24h)
    await fetchMaintenanceData()
    
    // On ferme et on reset
    showAddForm.value = false
    isEditing.value = false
    currentEditId.value = null
  } catch (e) {
    console.error("Erreur modification", e.response?.data)
  }
}

//fait un reset du formulaire sinon ajouter restera en "mettre à jours"
const resetFormAndOpen = () => {
  if (showAddForm.value) {
    // Si on ferme, on reset tout
    isEditing.value = false
    currentEditId.value = null
    form.value = { 
      date: new Date().toISOString().split('T')[0], 
      kilometrage: '', 
      description: '', 
      echeance_km: '', 
      echeance_date: '', 
      categorie_id: '', 
      car_id: route.params.id 
    }
  }
  showAddForm.value = !showAddForm.value
}




</script>

<style scoped>

/* Couleur de base (OK) */
.echeance-badge {
  color: white; 
  background: rgba(255, 255, 255, 0.1);
  font-size: 0.85em;
  padding: 2px 8px;
  border-radius: 4px;
  margin: 2px 0;
  display: inline-block;
}

/* Si l'échéance approche (Orange) */
.echeance-badge.soon {
  background: rgba(255, 165, 0, 0.2);
  color: #ffa500;
  border: 1px solid #ffa500;
}

/* Si l'échéance est dépassée (Rouge) */
.echeance-badge.overdue {
  background: rgba(255, 0, 0, 0.2);
  color: #ff4d4d;
  border: 1px solid #ff4d4d;
  font-weight: bold;
}


.mini-car-img{
  width: 50px;
}


/* Ta base reste la même */
.maintenance-container {
  background: #1a1a1a;
  color: white;
  padding: 20px;
  border-radius: 30px;
  border: 4px solid #2ecc71; /* Vert par défaut (ok) */
  transition: border-color 0.3s ease; /* Pour un changement de couleur fluide */
  margin: 20px;
}

/* On change la bordure selon l'état */
.maintenance-container.soon {
  border-color: #ffa500; /* Orange */
}

.maintenance-container.overdue {
  border-color: #ff4d4d; /* Rouge */
}

.maintenance-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.maintenance-table th {
  text-transform: capitalize;
  border-bottom: 2px solid white;
  padding: 10px;
}

.maintenance-table td {
  padding: 15px;
  border-bottom: 1px solid #444;
  text-align: center;
  max-width: 300px; 
}

.description-cell {
  max-width: 400px;
  word-wrap: break-word;    /* Force la coupure des mots trop longs */
  overflow-wrap: break-word;
  white-space: normal;      /* Autorise le retour à la ligne */
}

.car-info-badge {
  display: flex;
  align-items: center;
  gap: 20px;
  background: rgba(255,255,255,0.1);
  padding: 10px 30px;
  border-radius: 50px;
}

.table-footer {
  margin-top: 20px;
  display: flex;
  flex-direction: column; /* Pour empiler le formulaire et les boutons */
  gap: 15px;
}

.footer-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.add-form-overlay {
  background: #333;
  padding: 15px;
  border-radius: 20px;
  border: 1px solid #2ecc71; /* Rappel du vert */
}

.inline-form {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.inline-form input, .inline-form select {
  background: #1a1a1a;
  color: white;
  border: 1px solid #555;
  border-radius: 10px;
  padding: 8px;
  flex: 1;
}

.btn-confirm {
  background-color: #2ecc71;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
}



@media (max-width: 1024px) {
  /* On cache l'en-tête du tableau qui ne sert plus à rien */
  .maintenance-table thead {
    display: none;
  }

  /* Chaque ligne devient une "carte" */
  .maintenance-table tr {
    display: block;
    margin-bottom: 15px;
    border: 1px solid #444;
    border-radius: 15px;
    padding: 10px;
    background: rgba(255,255,255,0.05);
  }

  /* Chaque cellule s'affiche l'une sous l'autre */
  .maintenance-table td {
    display: flex;
    justify-content: space-between;
    text-align: right;
    border-bottom: 1px solid #333;
    padding: 8px 5px;
  }

  /* On peut rajouter le nom de la colonne avant la valeur avec du CSS */
  .maintenance-table td::before {
    content: attr(data-label); /* Il faudrait ajouter data-label="Date" sur tes <td> */
    font-weight: bold;
    color: #2ecc71;
    text-align: left;
  }
}
/* Ajoute tes autres styles ici pour coller à la photo */
</style>