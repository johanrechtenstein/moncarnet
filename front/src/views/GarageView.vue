<template>
  <div class="garage-page">
    <div class="garage-header">
      <h1 style="color: white;">Mon Garage</h1>
      
      <button v-if="!showForm" @click="showForm = true" class="btn-add">
        Ajouter
      </button>
    </div>

    <div v-if="showForm" class="add-car-inline">
        <form @submit.prevent="addVehicle" class="form-inline">
        <input v-model="newCar.marque" placeholder="Marque" required>
        <input v-model="newCar.modele" placeholder="Modèle" required>
        <input v-model="newCar.immatriculation" placeholder="Immatriculation" required>
        
        <div class="form-buttons">
          <button type="submit" class="btn-confirm">Confirmer</button>
          <button type="button" @click="showForm = false" class="btn-cancel">Annuler</button>
        </div>
      </form>
    </div>

    <div class="garage-content">
      <div v-if="cars.length === 0" style="color: gray;">
        Aucun véhicule dans votre garage.
      </div>
      
      <div v-else class="cars-grid">
        <div v-for="car in cars" :key="car.id" class="car-card-simple">
           <div class="car-name-tag">
          {{ car.marque }} {{ car.modele }}
      </div>
  
      <div style="margin: 20px 0;">
        <img src="https://via.placeholder.com/150" style="border-radius: 15px; width: 100%;">
      </div>

      <div class="plate-display">
        {{ car.immatriculation }}
      </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const cars = ref([]) // Notre liste vide au départ
const showForm = ref(false)

// Fonction pour récupérer les voitures
const fetchCars = async () => {
  try {
    const token = localStorage.getItem('user-token')
    const response = await axios.get('http://127.0.0.1:8000/api/cars', {
      headers: { Authorization: `Bearer ${token}` }
    })
    cars.value = response.data // On remplit notre liste avec la réponse
  } catch (error) {
    console.error("Erreur lors de la récupération :", error)
  }
}

// On lance la récupération dès que la page s'affiche
onMounted(() => {
  fetchCars()
})




// ajout de voiture

const newCar = ref({
  marque: '',          // Au lieu de brand
  modele: '',          // Au lieu de model
  immatriculation: '', // Au lieu de plate
  image_url: 'default_car.png' // Valeur par défaut pour l'instant
})

const addVehicle = async () => {
  try {
    const token = localStorage.getItem('user-token')
    const response = await axios.post('http://127.0.0.1:8000/api/cars', newCar.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    console.log("Véhicule ajouté !", response.data)
    
   // Reset du formulaire
    newCar.value = { marque: '', modele: '', immatriculation: '', image_url: 'default_car.png'  }
    showForm.value = false

    await fetchCars()
    
    // Ici, il faudra rafraîchir la liste (on verra ça juste après)
  } catch (error) {
    console.error("Erreur lors de l'ajout", error)
  }
}
</script>

<style scoped>



/* Container du formulaire */
.add-car-inline {
  background: rgba(255, 255, 255, 0.05);
  padding: 20px;
  border-radius: 15px;
  margin-bottom: 30px;
  border-left: 5px solid #27ae60;
}

/* Alignement horizontal du formulaire */
.form-inline {
  display: flex;
  flex-wrap: wrap; /* Pour que ça reste propre sur petit écran */
  gap: 15px;
  align-items: center;
}

.form-inline input {
  flex: 1; /* Les inputs prennent l'espace disponible */
  min-width: 150px;
  padding: 12px;
  border-radius: 8px;
  background: white;
  color: black;
}

/* Groupe de boutons à droite */
.form-buttons {
  display: flex;
  gap: 10px;
}

/* ---------------------------------------------------------------------------------------------------------------- */
.cars-grid {
  display: grid;
  /* Crée des colonnes de 300px minimum, autant qu'il en tient dans la largeur */
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
  gap: 30px;
  width: 100%;
  max-width: 1200px; /* Pour ne pas que ça s'étale trop sur les écrans géants */
  margin: 0 auto;    /* Centre la grille */
  padding: 20px;
}
/* Container principal */
.garage-page {
  padding: 10px 40px 40px; /* Espace pour la navbar */
  width: 100% ;
  margin: 30px;
  background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../assets/maintenance2.png'); /* Fond sombre */
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  color: white;
  border-radius: 30px;

}


/* La grille des voitures */
.garage-content {
  display: flex;
  justify-content: center;
  gap: 30px;
  flex-wrap: wrap; /* Pour que ça revienne à la ligne sur mobile */
  margin-top: 50px;
}

/* La Carte du véhicule (Style Maquette) */
.car-card-simple {
  background: #000;
  border: 4px solid #2ecc71; /* L'anneau vert par défaut */
  border-radius: 30px;
  padding: 20px;
  width: 250px;
  text-align: center;
  position: relative;
  box-shadow: 0 10px 20px rgba(0,0,0,0.5);
  transition: transform 0.3s ease;
}

.car-card-simple:hover {
  transform: translateY(-10px);
}

/* Le bandeau blanc pour le nom */
.car-name-tag {
  background: white;
  color: black;
  border-radius: 20px;
  padding: 5px 15px;
  font-weight: bold;
  margin-bottom: 15px;
  display: inline-block;
  width: 80%;
}

/* Style de l'immatriculation */
.plate-display {
  font-family: 'Courier New', Courier, monospace;
  background: #f0f0f0;
  color: #333;
  padding: 2px 8px;
  border-radius: 4px;
  font-weight: bold;
  letter-spacing: 1px;
}

/* Bouton Ajouter/confirmer */
.btn-add, .btn-confirm {
  background-color: #27ae60;
  color: white;
  border: none;
  padding: 15px 30px;
  border-radius: 25px;
  font-weight: bold;
  cursor: pointer;
  /* position: fixed; */
  bottom: 40px;
  right: 40px;
  transition: 0.3s;
}

.btn-cancel {
  background-color: #e74c3c; /* Rouge pour l'annulation */
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
}

.btn-cancel:hover {
  background-color: #c0392b;
}


.btn-add:hover {
  background-color: #2ecc71;
  transform: scale(1.05);
}

</style>