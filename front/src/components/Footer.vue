<template>
  <footer class="site-footer">
    <nav class="footer-nav">
        <router-link to="/contact" class="nav-item">Contact</router-link>
        <a href="#" class="nav-item">RGPD</a>
        <a href="#" class="nav-item">Mentions Légales</a>
    </nav>
    <p class="copyright">Mon Garage 2026</p>
  </footer>
</template>

<style scoped>
.site-footer {
  margin-top: auto; /* MAGIQUE : Ça pousse le footer tout en bas ! */
 background: linear-gradient(
    to top,            /* Direction du dégradé (du haut vers le bas) */
    rgba(0, 0, 0, 1) 80%,   
    rgba(0, 0, 0, 0) 100% 
  );
  color: white;
  padding: 20px;
  text-align: center;
}

.footer-nav {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 10px;
}

.footer-nav a {
  color: white;
  text-decoration: none;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.footer-nav a:hover {
  color:#FF6B35;
  transform: translateY(-2px);
}

.copyright {
  font-size: 0.8rem;
  opacity: 0.6;
}
</style>