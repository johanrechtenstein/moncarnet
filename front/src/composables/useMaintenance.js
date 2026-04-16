// src/composables/useMaintenance.js

export function useMaintenance() {
  
  // 1. La logique de calcul pure
  const getEcheanceStatus = (log, kmRef) => {
    if (!log || log.status === 'validated') return 'ok';
    
    const kmActuel = kmRef || 0;
    const dateActuelle = new Date();
    let urgency = 'ok';

    // Check Kilométrage
    if (log.echeance_km) {
      const reste = log.echeance_km - kmActuel;
      if (reste <= 0) urgency = 'overdue';
      else if (reste < 2000) urgency = 'soon'; // Tu avais mis 2000 ici, c'est pas mal !
    }

    // Check Date
    if (log.echeance_date && urgency !== 'overdue') {
      const jours = (new Date(log.echeance_date) - dateActuelle) / (1000 * 60 * 60 * 24);
      if (jours <= 0) urgency = 'overdue';
      else if (jours < 30) urgency = 'soon';
    }

    return urgency;
  };
// LE SCAN GLOBAL (pour tout le véhicule)
  const getVehicleStatus = (maintenances, kmVehicule) => {
    if (!maintenances || maintenances.length === 0) return 'ok';

    // On calcule le km réel (le max)
    const allKms = maintenances.map(m => m.kilometrage || 0);
    const kmActuel = Math.max(kmVehicule || 0, ...allKms);

    let highestUrgency = 'ok';

    for (const log of maintenances) {
      if (log.status !== 'validated' && !log.est_obsolete) {
        const status = getEcheanceStatus(log, kmActuel);
        if (status === 'overdue') return 'overdue';
        if (status === 'soon') highestUrgency = 'soon';
      }
    }
    return highestUrgency;
  };

  return { getEcheanceStatus, getVehicleStatus };

}