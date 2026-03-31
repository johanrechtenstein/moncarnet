import axios from 'axios';

// On crée une instance personnalisée
const api = axios.create({
    baseURL: 'http://localhost:8000/api', // L'URL de ton Laravel
    timeout: 5000, // Sécurité : on abandonne après 5s si le serveur ne répond pas
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Ce "middleware" (intercepteur) ajoute ton token à chaque appel
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default api;