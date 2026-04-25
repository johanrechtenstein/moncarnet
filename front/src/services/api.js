import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL, // ATTENTION : On enlève /api ici pour pouvoir appeler /sanctum/
    withCredentials: true,
    withXSRFToken: true,            // INDISPENSABLE pour les cookies
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Plus besoin d'intercepteur pour ajouter le "Bearer Token" !
// Le navigateur gère le cookie de session et le token CSRF automatiquement.

export default api;