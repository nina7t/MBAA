-- Insertion de 3 événements supplémentaires pour le carousel MBAA
-- À exécuter dans phpMyAdmin ou votre interface MySQL

INSERT INTO mbaa_evenement (titre, description, date_evenement, heure_debut, heure_fin, lieu, est_gratuit, prix, image_url, id_type_evenement, date_creation, date_modification) VALUES
(
    'Atelier découverte de la photographie analogique',
    'Initiez-vous aux techniques de la photographie argentique dans cet atelier pratique. Apprenez à développer vos propres films et à créer des tirages en chambre noire.',
    '2026-04-15',
    '14:00:00',
    '17:00:00',
    'Atelier MBAA - Labo photo',
    1,
    NULL,
    '/wp-content/themes/Mbaa_theme/asset/Img/atelier-photo.jpg',
    (SELECT id_type_evenement FROM mbaa_type_evenement WHERE nom_type = 'Atelier' LIMIT 1),
    NOW(),
    NOW()
),
(
    'Visite guidée : Les coulisses du musée',
    'Découvrez les zones privées du musée, les réserves et le processus de conservation des œuvres. Une visite exclusive menée par les conservateurs.',
    '2026-04-20',
    '10:00:00',
    '12:00:00',
    'Musée MBAA - Entrée principale',
    0,
    15.00,
    '/wp-content/themes/Mbaa_theme/asset/Img/visite-musee.jpg',
    (SELECT id_type_evenement FROM mbaa_type_evenement WHERE nom_type = 'Visite' LIMIT 1),
    NOW(),
    NOW()
),
(
    'Conférence : L\'art contemporain et les nouvelles technologies',
    'Explorez l\'intersection entre l\'art traditionnel et les technologies numériques. Conférence avec des artistes et chercheurs innovants.',
    '2026-05-08',
    '18:30:00',
    '20:30:00',
    'Auditorium MBAA',
    0,
    12.50,
    '/wp-content/themes/Mbaa_theme/asset/Img/conference-tech.jpg',
    (SELECT id_type_evenement FROM mbaa_type_evenement WHERE nom_type = 'Conférence' LIMIT 1),
    NOW(),
    NOW()
);

-- Vérification des événements insérés
SELECT * FROM mbaa_evenement 
WHERE date_evenement >= CURDATE() 
ORDER BY date_evenement ASC 
LIMIT 15;
