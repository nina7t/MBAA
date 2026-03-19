-- Insertion d'événements pour le MBAA avec les bons noms de tables
-- À exécuter dans phpMyAdmin ou votre interface MySQL

-- Vérification d'abord que les tables existent
SHOW TABLES LIKE '%mbaa%evenement%';

-- Insertion des types d'événements s'ils n'existent pas
INSERT IGNORE INTO wp_mbaa_types_evenements (id_type, nom_type, type_categorie, date_creation) VALUES
(1, 'Atelier', 'atelier', NOW()),
(2, 'Visite', 'visite', NOW()),
(3, 'Conférence', 'conference', NOW()),
(4, 'Concert', 'concert', NOW()),
(5, 'Soirée', 'soiree', NOW());

-- Insertion des événements avec les bonnes images existantes
INSERT INTO wp_mbaa_evenements (
    titre, 
    descriptif, 
    date_evenement, 
    heure_debut, 
    heure_fin, 
    lieu_musee, 
    est_gratuit, 
    prix, 
    image_url, 
    id_type,
    public_enfant,
    public_ado, 
    public_adulte,
    public_tout_public,
    date_creation,
    date_modification
) VALUES
(
    'Atelier découverte de la photographie',
    'Initiez-vous aux techniques photographiques dans cet atelier pratique.',
    '2026-04-15',
    '14:00:00',
    '17:00:00',
    'Atelier MBAA',
    1,
    NULL,
    '/wp-content/themes/Mbaa_theme/asset/Img/Evenement/evenement-beaux-arts.jpg',
    1,
    0, 0, 1, 0,
    NOW(),
    NOW()
),
(
    'Visite guidée : Les coulisses du musée',
    'Découvrez les zones privées du musée et les réserves.',
    '2026-04-20',
    '10:00:00',
    '12:00:00',
    'Musée MBAA',
    0,
    15.00,
    '/wp-content/themes/Mbaa_theme/asset/Img/Evenement/evenement-visite-guidee.jpg',
    2,
    1, 1, 1, 1,
    NOW(),
    NOW()
),
(
    'Concert Jazz au musée',
    'Soirée jazz exceptionnelle dans les galeries du musée.',
    '2026-05-08',
    '18:30:00',
    '22:30:00',
    'Galerie principale',
    0,
    25.00,
    '/wp-content/themes/Mbaa_theme/asset/Img/Evenement/evenement-concert.jpg',
    4,
    0, 1, 1, 1,
    NOW(),
    NOW()
),
(
    'Nuit Européenne des Musées',
    'Soirée spéciale avec performances et visites nocturnes.',
    '2026-05-15',
    '20:00:00',
    '00:00:00',
    'Tout le musée',
    1,
    NULL,
    '/wp-content/themes/Mbaa_theme/asset/Img/Evenement/evenement-lumiere-musee.jpg',
    5,
    1, 1, 1, 1,
    NOW(),
    NOW()
),
(
    'Atelier peinture pour enfants',
    'Atelier créatif dédié aux jeunes artistes en herbe.',
    '2026-04-22',
    '15:00:00',
    '17:00:00',
    'Atelier MBAA',
    1,
    NULL,
    '/wp-content/themes/Mbaa_theme/asset/Img/Evenement/evenement-beaux-arts.jpg',
    1,
    1, 0, 0, 0,
    NOW(),
    NOW()
);

-- Vérification
SELECT * FROM wp_mbaa_evenements 
WHERE date_evenement >= CURDATE() 
ORDER BY date_evenement ASC;
