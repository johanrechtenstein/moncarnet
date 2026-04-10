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
              <th>description</th>
              <th>facture</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in maintenanceLogs" :key="log.id">
              <td>{{ log.categorie }}</td>
              <td>{{ new Date(log.date).toLocaleDateString() }}</td>
              <td>{{ log.kilometrage }} km</td>
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
          <label><input type="checkbox"> cacher les mises à jours</label>
          <button class="btn-add-log">Ajouter</button>
        </div>
      </div>

      <div class="update-footer">
        <span>Mise à jours du kilométrage :</span>
        <input type="number" class="input-short">
        <span>Date :</span>
        <input type="date" class="input-short">
        <button class="btn-update">mettre à jours</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const car = ref({})
const maintenanceLogs = ref([])
const loading = ref(true)

const fetchMaintenanceData = async () => {
  try {
    const token = localStorage.getItem('user-token')
    const carId = route.params.id

    const res = await axios.get(`http://127.0.0.1:8000/api/cars/${carId}`,{
      headers: { Authorization: `Bearer ${token}` }
    } )
    
    // car.value contiendra les infos de la voiture (immatriculation, etc.)
    car.value = res.data
    // maintenanceLogs contiendra la liste liée (grâce au ->with('maintenances'))
    maintenanceLogs.value = res.data.maintenances || []
  } catch (error) {
    console.error("Erreur de chargement :", error)
  }finally {
    loading.value = false // S'exécute quoi qu'il arrive (succès ou erreur)
  }
}

onMounted(fetchMaintenanceData)
</script>

<style scoped>
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
/* Ajoute tes autres styles ici pour coller à la photo */
</style>