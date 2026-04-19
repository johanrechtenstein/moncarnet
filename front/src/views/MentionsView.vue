<template>
  <div class="mention-page">
    <div class="mention-container">
      <div class="mention-card">
        <h1>Mentions Légales & RGPD</h1>

        <section>
          <h2>1. Mentions Légales</h2>
          <div class="info-block">
            <p>Le site <strong>Mon Garage</strong> est édité par :</p>
            <ul>
              <li><strong>Nom / Prénom :</strong> RECHTENSTEIN Johan</li>
              <li><strong>Statut :</strong> Particulier</li>
              <li><strong>Email :</strong> maxitunnig67@gmail.com</li>
            </ul>
          </div>

          <div class="info-block">
            <h3>Hébergement</h3>
            <p>Le site est hébergé par : (en attente d'hébergeur)</p>
            <ul>
              <li><strong>Nom :</strong> [Ex: Hostinger, OVH, Vercel]</li>
              <li><strong>Adresse :</strong> [Adresse physique de l'hébergeur]</li>
            </ul>
          </div>
        </section>

        <section>
          <h2>2. Politique de Confidentialité (RGPD)</h2>
          
          <div class="info-block">
            <h3>Données collectées</h3>
            <p>Nous collectons uniquement les données nécessaires au bon fonctionnement du service :</p>
            <ul>
              <li><strong>Profil :</strong> Pseudo, adresse email.</li>
              <li><strong>Véhicules :</strong> Informations techniques (marque, modèle, numéro d'identification etc.).</li>
              <li><strong>Entretiens :</strong> Historique, dates, kilométrages.</li>
            </ul>
          </div>

          <div class="info-block">
            <h3>Finalité de la collecte</h3>
            <ul>
              <li>Gestion du compte utilisateur.</li>
              <li>Sauvegarde des carnets d'entretien numériques.</li>
              <li>Réinitialisation de mot de passe.</li>
              <li>Traitement des demandes via le formulaire de contact.</li>
            </ul>
          </div>

          <div class="info-block">
            <h3>Durée de conservation & Suppression</h3>
            <p>
              Les données sont conservées tant que votre compte est actif. 
              <strong>En cas de suppression de compte</strong>, toutes les données associées sont immédiatement 
              et définitivement supprimées de notre base de données.
            </p>
          </div>

         
            <div class="info-block">
            <h3>Vos Droits</h3>
            <p>
             Conformément au RGPD, vous disposez d'un droit d'accès, de rectification (uniquement pour votre adresse email) 
            et d'effacement de la totalité de vos données, exerçable directement via votre espace profil ou par email.
            </p>
            <p>
            Afin de garantir votre <strong>anonymat</strong>, nous ne collectons ni votre nom, ni votre prénom. 
            L'identification se fait exclusivement via votre pseudo (non modifiable pour garantir la stabilité de l'historique). 
            L'adresse mail sert uniquement en cas de changement de mot de passe pour vous permettre d'obtenir un lien sécurisé.
            </p>
            </div>

             <div class="info-block">
            <h3>Intégrité et Traçabilité des données</h3>
            <p>Afin de garantir l'authenticité du carnet d'entretien numérique et d'apporter une valeur de preuve lors de la revente du véhicule, 
                le numéro de d'identification du véhicule n'est pas modifiable, toute saisie d'entretien devient non modifiable et non supprimable par 
                l'utilisateur passé un délai de 24 heures. Ce verrouillage technique assure la fiabilité de l'historique du véhicule..</p>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<style scoped>
.mention-page {
  display: flex;
  justify-content: center;
  align-items: flex-start; /* Permet de scroller si le texte est long */
  min-height: 100vh;
  width: 100%;
  padding: 40px 0;
}

.mention-container {
  max-width: 900px; /* Plus étroit pour faciliter la lecture du texte */
  width: 95%;
}

.mention-card {
  background-color: rgba(0, 0, 0, 0.9);
  border: 4px solid white;
  border-radius: 15px;
  padding: 40px;
  color: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  line-height: 1.6;
}

h1 {
  text-align: center;
  color: #FF6B35; /* Ton orange pour le titre principal */
  margin-bottom: 30px;
  text-transform: uppercase;
}

h2 {
  border-bottom: 1px solid #FF6B35;
  padding-bottom: 10px;
  margin-top: 40px;
  font-size: 1.5rem;
}

h3 {
  color: #FF6B35;
  margin-top: 20px;
  font-size: 1.2rem;
}

.info-block {
  margin-bottom: 20px;
}

ul {
  list-style-type: none; /* Plus propre avec ton design */
  padding-left: 0;
}

li {
  margin-bottom: 8px;
}

li strong {
  color: #ccc;
}

p {
  margin-bottom: 15px;
}

/* Responsive */
@media (max-width: 600px) {
  .mention-card {
    padding: 20px;
    font-size: 0.95rem;
  }

  h1 {
    font-size: 1.4rem;
  }
}
</style>