<template>
  <div class="hero-container">
    <div class="hero-text">
      <h1>Votre passion mérite le <span class="highlight">digital</span></h1>
      <p>"Suivez l'<span class="highlight">entretien</span> de votre véhicule en temps réel, car son <span class="highlight">passé</span> vous le rendra dans le <span class="highlight">futur</span>."</p>
    </div>

    <div class="hero-form">
      <div class="form-group">
        <label>Pseudo :</label>
        <input type="text" />
      </div>
      <div class="form-group">
        <label>Mot de passe :</label>
        <input type="password" />
      </div>
      <button class="btn-connexion">Connexion</button>
    </div>
  </div>
</template>

<style scoped>
.hero-container {
  display: flex;
  justify-content: space-evenly; /* Espace entre les deux blocs */
  /*align-items: center;*/
  align-items: flex-start;
  padding: 0 5%;
  flex-grow: 1; /* Prend tout l'espace central entre la nav et le footer */
  padding-top: 15rem;
}

/* Style commun aux deux blocs (le fond noir transparent) */
.hero-text, .hero-form {
  background: rgba(0, 0, 0, 0.75); /* Noir bien opaque */
  backdrop-filter: blur(5px);      /* Floute légèrement l'image derrière (très moderne) */
  padding: 30px;
  border-radius: 50px;             /* Bords très arrondis comme sur ta maquette */
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.1); /* Petit liseré discret */
}

.hero-text {
  max-width: 460px;
  text-align: center;
}

.highlight {
  color: #FF6B35;
}

.hero-form {
  width: 320px; /* On stabilise la largeur de la capsule */
  display: flex;
  flex-direction: column;
  gap: 20px;
  align-items: center; /* Centre le bouton Connexion */
  padding: 40px 30px;
  box-sizing: border-box;
  margin-left: 20px;
  margin-top: 50px;
}

input {
  width: 100%;
  padding: 12px;
  border-radius: 25px; /* Inputs arrondis aussi */
  border: none;
  margin-top: 5px;
  box-sizing: border-box;
  
}

.btn-connexion {
  background-color: #FF6B35;
  color: white;
  border: none;
  padding: 15px;
  border-radius: 30px;
  font-weight: bold;
  cursor: pointer;
  margin-top: 10px;
  width: 180px;
  transition: all 0.3s ease;
}

.btn-connexion:hover {
  background-color: white; /* Un orange un peu plus sombre au survol */
  transform: translateY(-2px); /* Un léger mouvement vers le haut */
  color:#FF6B35;
}




/* --- LE BLOC MAGIQUE POUR MOBILE --- */
@media (max-width: 768px) {
  .hero-container {
    flex-direction: column; /* On empile au lieu de mettre côte à côte */
    align-items: center;    /* On centre tout le monde */
    padding-top: 5rem;      /* On remonte un peu le tout */
    gap: 20px;              /* On laisse respirer entre les deux boîtes */
  }

  .hero-text, .hero-form {
    max-width: 90%;        /* Les boîtes prennent presque toute la largeur */
    margin-left: 0;        /* On enlève tes décalages de "margin" */
    margin-top: 0;
    padding: 30px;         /* On réduit un peu le padding interne */
  }

  h1 {
    font-size: 1.5rem;     /* On réduit la taille du titre */
  }
}
</style>