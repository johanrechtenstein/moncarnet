<template>
  <nav class="navbar">
    <div class="logo">
      <img src="../assets/logo2.png" alt="Logo MG" class="logo-img">
    </div>

    <div class="nav-links">
      <a href="#" class="nav-item">Contact</a>
      <button class="btn-inscription">Inscription</button>
    </div>
  </nav>
</template>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between; /* Logo à gauche, Liens à droite */
  align-items: center; /* Centré verticalement */
  padding: 30px 60px; /* Un peu plus d'espace */
  
  background: linear-gradient(
    to bottom,            /* Direction du dégradé (du haut vers le bas) */
    rgba(0, 0, 0, 1) 80%,   
    rgba(0, 0, 0, 0) 100% 
  );
  
  /* On positionne la navbar en haut de l'écran */
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 10; /* Assure qu'elle est bien DEVANT l'image de fond */
  box-sizing: border-box; /* Évite que le padding ne déforme la largeur */
}

.logo-img {
  height: 50px; /* Ajuste la hauteur selon tes envies */
  width: auto;  /* Garde les proportions */
  display: block;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 40px; /* Plus d'espace entre les menus */
}

.nav-item {
  color: white;
  text-decoration: none;
  font-weight: 500;
  font-size: 1.1rem;
  transition: all 0.3s ease;
}

.nav-item:hover {
  color:#FF6B35;
  transform: translateY(-2px);
}

.btn-inscription {
  /* Fond orange saumon, texte noir */
  background-color: #FF6B35; 
  color: white;
  
  border: none;
  padding: 12px 28px; /* Un bouton un peu plus grand */
  border-radius: 25px;
  font-weight: bold;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-inscription:hover {
  background-color: white; /* Un orange un peu plus sombre au survol */
  transform: translateY(-2px); /* Un léger mouvement vers le haut */
  color:#FF6B35;
}
</style>