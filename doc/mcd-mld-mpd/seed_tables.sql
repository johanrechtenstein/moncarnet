BEGIN;

-- On vide les tables avant de les remplir (au cas où)
-- TRUNCATE efface les données et redémarre les compteurs d'IDENTITY
TRUNCATE TABLE "maintenance", "voiture", "user" RESTART IDENTITY CASCADE;

-- 1. Insertion des Utilisateurs
INSERT INTO "user" ("pseudo", "email", "password", "avatar_url") VALUES
('Jean_Dupont', 'jean.dupont@example.com', '$2b$10$w7kQ...', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Jean'),
('Sarah_Connor', 'sarah.c@skynet.com', '$2b$10$x8mR...', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah'),
('Thomas_R', 'thomas.r@protonmail.com', '$2b$10$z9pL...', NULL);

-- 2. Insertion des Voitures
-- On lie les voitures aux IDs des utilisateurs créés ci-dessus
INSERT INTO "voiture" ("marque", "modele", "immatriculation", "user_id", "image_url") VALUES
('Peugeot', '208', 'AB-123-CD', 1, 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d'),
('Tesla', 'Model 3', 'EV-555-ZZ', 2, 'https://images.unsplash.com/photo-1560958089-b8a1929cea89'),
('Volkswagen', 'Golf 8', 'GH-789-JK', 1, 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d');

-- 3. Insertion des Maintenances
INSERT INTO "maintenance" ("categorie", "date", "kilometrage", "description", "voiture_id") VALUES
('Vidange', '2023-05-15', 15200, 'Remplacement huile moteur et filtre à huile', 1),
('Pneumatiques', '2023-11-20', 25000, 'Changement pneus avant (Michelin)', 2);

-- Insertion d'une maintenance avec un parent_id (exemple : contre-visite ou suivi)
INSERT INTO "maintenance" ("categorie", "date", "kilometrage", "description", "voiture_id", "parent_id") VALUES
('Révision', '2024-02-10', 30500, 'Révision annuelle complète', 1, 1);

COMMIT;