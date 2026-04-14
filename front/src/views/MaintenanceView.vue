<template>
  <div v-if="loading" class="loader-container">
    <div class="loader">Chargement du garage...</div>
  </div>

  <div v-else class="maintenance-page-wrapper">
    <div class="maintenance-container">
      
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
              <td>{{ log.categorie?.nom || 'N/A' }}</td>
              <td>{{ new Date(log.date).toLocaleDateString() }}</td>
              <td>{{ log.kilometrage }} km</td>
              <td>
                <div v-if="log.echeance_km" class="echeance-badge km">
                {{ log.echeance_km }} km
                </div>
                <div v-if="log.echeance_date" class="echeance-badge date">
                {{ new Date(log.echeance_date).toLocaleDateString() }}
                </div>
                <span v-if="!log.echeance_km && !log.echeance_date">-</span>
                </td>
              <td>{{ log.description }}</td>
              <td>{{ log.facture_url || '-' }}</td>
              <td><span class="edit-icon" style="cursor:pointer">✏️</span></td>
            </tr>
            <tr v-if="maintenanceLogs.length === 0">
              <td colspan="6" style="padding: 20px; color: gray;">Aucun historique pour ce véhicule.</td>
            </tr>
          </tbody>
        </table>
        
        <div class="table-footer">
        
  <div class="footer-controls">
    <label><input type="checkbox" v-model="hideReleves"> cacher les relevés</label>
    
    <button class="btn-add-log" @click="showAddForm = !showAddForm">
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
      
      <button class="btn-confirm" @click="addMaintenance">Enregistrer</button>
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

const route = useRoute()
const car = ref({})
const maintenanceLogs = ref([])
const categories = ref([])
const loading = ref(true)
const showAddForm = ref(false) 
const hideReleves = ref(false)

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
</script>

<style scoped>

.mini-car-img{
width: 50px;

}


.maintenance-container {
  background: #1a1a1a;
  color: white;
  padding: 20px;
  border-radius: 30px;
  border: 4px solid #2ecc71; /* Le contour vert de ta maquette */
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


.echeance-badge {
  font-size: 0.85em;
  padding: 2px 8px;
  border-radius: 4px;
  margin: 2px 0;
  display: inline-block;
}

.echeance-badge.km {
  background: rgba(46, 204, 113, 0.1); /* Vert très léger */
  color: #2ecc71;
  border: 1px solid rgba(46, 204, 113, 0.3);
}

.echeance-badge.date {
  background: rgba(52, 152, 219, 0.1); /* Bleu très léger */
  color: #3498db;
  border: 1px solid rgba(52, 152, 219, 0.3);
}
/* Ajoute tes autres styles ici pour coller à la photo */
</style>