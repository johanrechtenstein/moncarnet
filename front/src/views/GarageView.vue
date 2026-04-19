<template>
  <div class="garage-page">

    <h1 style="color: white;">Garage de {{ user.pseudo }}</h1>

    <div class="garage-content">
      <div v-if="cars.length === 0" style="color: gray;">
        Aucun véhicule dans votre garage.
      </div>
      
      <div v-else class="cars-grid">
        <div v-for="car in cars" 
        :key="car.id" class="car-card-simple" 
        :class="getVehicleStatus(car.maintenances, car.kilometrage)" 
        @click="goToMaintenance(car.id)" 
         style="cursor: pointer;">
         
          <button @click.stop="deleteCar(car.id)" class="delete-icon">×</button>
           <div class="car-name-tag">
          {{ car.marque }} {{ car.modele }}
      </div>
  
      <div style="margin: 20px 0;">
        <img src="../assets/voiture1.png" style="border-radius: 15px; width: 50%;">
      </div>

     <div class="plate-container" @click.stop> <div v-if="editingId !== car.id" @click="startEdit(car)" class="plate-display clickable-plate">
    {{ car.immatriculation }}
    <span class="edit-hint">✎</span>
  </div>

  <div v-else class="plate-edit">
  <input 
    v-model="tempPlate" 
    @keyup.enter="updatePlate(car)" 
    @blur="updatePlate(car)"
    v-focus
    class="plate-input"
    placeholder="AA-123-BB"
  >
</div>
</div>

      <div class="vin-display">
            VIN: {{ car.vin }}
          </div>
        </div>
      </div>
    </div>
    <div class="garage-footer">
      
      <button v-if="!showForm" @click="showForm = true" class="btn-add">
        Ajouter
      </button>
    </div>

    <div v-if="showForm" class="add-car-inline">
        <form @submit.prevent="addVehicle" class="form-inline">
        <input v-model="newCar.marque" placeholder="Marque" required>
        <input v-model="newCar.modele" placeholder="Modèle" required>
        <input v-model="newCar.vin" placeholder="VIN (Optionnel)" maxlength="17" style="text-transform: uppercase;">
        <input v-model="newCar.immatriculation" placeholder="Immatriculation" required>
        
        <div class="form-buttons">
          <button type="submit" class="btn-confirm">Confirmer</button>
          <button type="button" @click="showForm = false" class="btn-cancel">Annuler</button>
        </div>
      </form>
    </div>


  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useMaintenance } from '../composables/useMaintenance'

const router = useRouter()
const user = ref({ pseudo: 'Mécanicien' })
const cars = ref([]) // Notre liste vide au départ
const showForm = ref(false)
const editingId = ref(null)
const tempPlate = ref('')
const vFocus = { mounted: (el) => el.focus() }

// Active le mode édition pour une carte
const startEdit = (car) => {
  editingId.value = car.id
  tempPlate.value = car.immatriculation
}

// fonction état du véhicule
const { getVehicleStatus } = useMaintenance()

const newCar = ref({
  marque: '',          // Au lieu de brand
  modele: '',          // Au lieu de model
  vin: '',
  immatriculation: '', // Au lieu de plate
  image_url: 'default_car.png' // Valeur par défaut pour l'instant
})

// Force les majuscules sur le VIN pendant la saisie
watch(() => newCar.value.vin, (newVal) => {
  if (newVal) newCar.value.vin = newVal.toUpperCase()
})


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


const goToMaintenance = (carId) => {
  // On redirige vers une route dynamique, ex: /maintenance/5
  router.push(`/maintenance/${carId}`)
}



// On lance la récupération dès que la page s'affiche
onMounted(() => {
  const savedPseudo = localStorage.getItem('user-pseudo')
  if (savedPseudo) if (savedPseudo) {
    // Si savedPseudo est "Jean", user.value.pseudo devient "Jean"
    user.value.pseudo = savedPseudo
  } else {
    user.value.pseudo = 'Mécanicien'
  }
 
  fetchCars()

})


const addVehicle = async () => {
  try {
    const token = localStorage.getItem('user-token')
    const response = await axios.post('http://127.0.0.1:8000/api/cars', newCar.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    console.log("Véhicule ajouté !", response.data)
    
   // Reset du formulaire
    newCar.value = { marque: '', modele: '', vin: '', immatriculation: '', image_url: 'default_car.png'  }
    showForm.value = false
    await fetchCars()
    // Ici, il faudra rafraîchir la liste (on verra ça juste après)
  } catch (error) {
    console.error("Erreur lors de l'ajout", error)
  }
}

// suppression de voiture
const deleteCar = async (id) => {
  if (confirm("Voulez-vous vraiment retirer ce véhicule du garage ?")) {
    try {
      const token = localStorage.getItem('user-token')
      await axios.delete(`http://127.0.0.1:8000/api/cars/${id}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      
      // On recharge la liste pour mettre à jour l'affichage
      fetchCars()
    } catch (error) {
      console.error("Erreur lors de la suppression :", error)
    }
  }
}


// Enregistre la nouvelle plaque
const updatePlate = async (car) => {
  if (!tempPlate.value || tempPlate.value === car.immatriculation) {
    editingId.value = null
    return
  }

  try {
    const token = localStorage.getItem('user-token')
    await axios.put(`http://127.0.0.1:8000/api/cars/${car.id}`, 
      { immatriculation: tempPlate.value },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    
    editingId.value = null
    fetchCars() // On rafraîchit la liste
  } catch (error) {
    console.error("Erreur lors de la modification de la plaque :", error)
    alert("Cette plaque est peut-être déjà utilisée.")
  }
}


</script>

<style scoped>

h1 {
  color: white;
  margin-left: 20px; /* Ajuste cette valeur (10px, 30px, etc.) selon tes goûts */
  margin-bottom: 10px; /* Un petit espace avec le contenu du dessous */
  text-align: left;    /* Assure qu'il reste à gauche */
}


/* Container du formulaire */
.add-car-inline {
  background: rgba(0, 0, 0, 0.8);
  padding: 20px;
  border-radius: 15px;
  margin-bottom: 10px;
  border-left: 5px solid #FF6B35;
}

.garage-footer{
  margin-top: 20px;
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
  /* On utilise auto-fit et une taille fixe (300px) au lieu de 1fr */
  grid-template-columns: repeat(auto-fit, 300px); 
  gap: 30px;
  width: 100%;
  justify-content: center;   /* Centre les colonnes dans la grille */
  align-content: center;    
  margin: 0 auto;           /* Centre le bloc complet */
}
/* Container principal */
.garage-page {
  margin: 20px auto; /* Centre le bloc et laisse un peu d'air en haut/bas */
  width: 95%;        /* Prend 95% de l'écran, donc 2.5% de marge de chaque côté */
  max-width: 1400px; /* Mais ne devient pas géant sur un écran 4K */
  padding: 60px 20px;
  background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../assets/maintenance2.png'); /* Fond sombre */
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  color: white;
  border-radius: 30px;
  box-sizing: border-box;
}


/* La grille des voitures */
.garage-content {
  display: flex;
  justify-content: center;
 
 
  margin-top: 50px;
}

/* La Carte du véhicule (Style Maquette) */
.car-card-simple {
  background: #000;
  /* border: 4px solid #2ecc71; L'anneau vert par défaut */
  border-radius: 30px;
  padding: 20px;
  width: 250px;
  text-align: center;
  position: relative;
  box-shadow: 0 10px 20px rgba(0,0,0,0.2);
  transition: transform 0.3s ease;
}

.car-card-simple:hover {
  transform: translateY(-10px);
}


/* Couleur par défaut (si tout va bien) */
.car-card-simple.ok {
  border: 2px solid #2ecc71;
}

/* Si une échéance approche (Orange) */
.car-card-simple.soon {
  border: 2px solid #ffa500;
  box-shadow: 0 0 10px rgba(255, 165, 0, 0.2);
}

/* Si une échéance est dépassée (Rouge) */
.car-card-simple.overdue {
  border: 2px solid #ff4d4d;
  box-shadow: 0 0 15px rgba(255, 77, 77, 0.3);
}

/* Le bandeau blanc pour le nom */
.car-name-tag, .plate-display {
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
  position: relative; /* Indispensable */
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


.delete-icon {
  position: absolute;
  top: 10px;
  right: 15px;
  background: red;
  border-radius: 50%;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  transition: transform 0.2s;
  
 
}

.delete-icon:hover {
  
  transform: scale(1.3);
}

/* .clickable-plate {
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 5px 15px;
  transition: background-color 0.2s;
} */

.clickable-plate:hover {
  background-color: #e0e0e0; /* Un léger gris au survol pour montrer que c'est cliquable */
}

/* Le crayon dans l'angle supérieur droit */
.edit-hint {
  position: absolute;
  top: -5px;    /* Sort légèrement vers le haut */
  right: -5px;  /* Sort légèrement vers la droite */
  
  background: white;    /* Petit fond blanc pour le détacher */
  border: 1px solid #FF6B35;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  
  color: #FF6B35;
  font-size: 0.9rem;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  transition: transform 0.2s;
}

.edit-hint:hover {
  transform: scale(1.2);
  background-color: #FF6B35;
  color: white;
}


.plate-input {
  /* On copie le look de la plaque */
  background: white;
  color: black;
  border-radius: 20px;
  padding: 5px 15px;
  font-weight: bold;
  
  /* On enlève les bordures d'input par défaut */
  border: none;
  outline: none; /* Enlève le contour bleu au clic */
  
  /* On centre le texte et on force les majuscules */
  text-align: center;
  text-transform: uppercase;
  
  /* On s'assure qu'il prend la même place que la plaque */
  width: 80%; 
  display: inline-block;
  font-family: inherit; /* Utilise la même police que le reste */
  font-size: 1rem;
}

.vin-display {
  font-size: 0.7rem;
  color: #888;
  margin-top: 5px;
}


@media (max-width: 740px) {
  /* On passe le formulaire en colonne */
  .form-inline {
    flex-direction: column;
    align-items: stretch;
  }
}

</style>