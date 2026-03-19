BEGIN;

-- On vide toutes les tables (ordre inverse des créations pour les FK)
TRUNCATE TABLE "maintenance", "categorie", "voiture", "user" RESTART IDENTITY CASCADE;

-- 1. Insertion des Utilisateurs
INSERT INTO "user" ("pseudo", "email", "password", "avatar_url") VALUES
('Jean_Dupont', 'jean.dupont@example.com', '$2b$10$w7kQ...', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Jean'),
('Sarah_Connor', 'sarah.c@skynet.com', '$2b$10$x8mR...', 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah'),
('Thomas_R', 'thomas.r@protonmail.com', '$2b$10$z9pL...', NULL);

-- 2. Insertion des Catégories (Crucial pour ton nouveau SQL)
-- On force les IDs pour être sûr de nos références plus bas
INSERT INTO "categorie" ("id", "nom") OVERRIDING SYSTEM VALUE VALUES
(1, 'MAJ'),
(2, 'entretien'),
(3, 'réparation');

-- 3. Insertion des Voitures
-- On utilise l'immatriculation UNIQUE que tu as ajoutée
INSERT INTO "voiture" ("marque", "modele", "immatriculation", "user_id", "image_url") VALUES
('Peugeot', '208', 'AB-123-CD', 1, 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d'),
('Tesla', 'Model 3', 'EV-555-ZZ', 2, 'https://images.unsplash.com/photo-1560958089-b8a1929cea89'),
('Volkswagen', 'Golf 8', 'GH-789-JK', 1, 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d');

-- 4. Insertion des Maintenances
-- On remplace la colonne "categorie" par "categorie_id"
INSERT INTO "maintenance" ("categorie_id", "date", "kilometrage", "description", "voiture_id") VALUES
(2, '2023-05-15', 15200, 'Vidange moteur et filtre', 1), -- ID 2 = entretien
(3, '2023-11-20', 25000, 'Changement pneus avant', 2),    -- ID 3 = réparation
(1, '2024-01-01', 16000, 'Relevé mensuel', 1);             -- ID 1 = MAJ

-- Insertion d'une maintenance liée (parent_id)
INSERT INTO "maintenance" ("categorie_id", "date", "kilometrage", "description", "voiture_id", "parent_id") VALUES
(2, '2024-02-10', 30500, 'Révision annuelle suite à vidange', 1, 1);

COMMIT;