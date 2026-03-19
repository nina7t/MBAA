# MBAA - Plugin de Gestion de Musée pour WordPress

## Description

Plugin WordPress complet pour la gestion d'un musée avec base de données SQL personnalisée (pas de Custom Post Types).

### Fonctionnalités

- **Artistes** : Gestion complète des artistes (nom, biographie, dates, nationalité, photo)
- **Œuvres** : Gestion des œuvres d'art avec relations vers artistes, époques, salles, mouvements
- **Événements** : Création et gestion d'événements (ateliers, visites, conférences)
- **Audioguides** : Ajout d'audioguides pour les œuvres
- **Expositions** : Organisation d'expositions temporaires
- **Paramètres** : Gestion des époques, salles, mouvements artistiques, médiums, etc.

## Installation

1. Télécharger le plugin
2. Placer le dossier dans `/wp-content/plugins/`
3. Activer le plugin dans WordPress
4. Les tables seront créées automatiquement à l'activation

## Structure du plugin

```
mbaa-plugin/
├── mbaa-plugin.php                 # Fichier principal
├── includes/
│   ├── class-mbaa-database.php     # Gestion de la base de données
│   ├── class-mbaa-admin.php        # Interface d'administration
│   ├── class-mbaa-artiste.php      # CRUD Artistes
│   ├── class-mbaa-oeuvre.php       # CRUD Œuvres
│   ├── class-mbaa-evenement.php    # CRUD Événements
│   └── class-mbaa-audioguide.php   # CRUD Audioguides
├── views/
│   ├── dashboard.php               # Tableau de bord
│   ├── artistes-list.php           # Liste des artistes
│   ├── artiste-form.php            # Formulaire artiste
│   ├── oeuvres-list.php            # Liste des œuvres
│   ├── oeuvre-form.php             # Formulaire œuvre
│   ├── evenements-list.php         # Liste des événements
│   ├── evenement-form.php          # Formulaire événement
│   ├── audioguides-list.php        # Liste des audioguides
│   ├── audioguide-form.php         # Formulaire audioguide
│   └── parametres.php              # Page de paramètres
└── assets/
    ├── css/
    │   └── admin-style.css         # Styles admin
    └── js/
        └── admin-script.js         # Scripts admin
```

## Structure de la base de données

### Tables principales

1. **wp_mbaa_artiste**

   - id_artiste (PK)
   - nom, biographie
   - date_naissance, date_deces
   - nationalite, image_url
   - creation, mis_a_jour

2. **wp_mbaa_oeuvre**

   - id_oeuvre (PK)
   - titre, description
   - date_creation, dimensions
   - numero_inventaire, technique
   - image_url
   - id_artiste (FK), id_epoque (FK), id_salle (FK)
   - id_medium (FK), id_mouvement (FK), id_categorie (FK)
   - visible_galerie, visible_accueil
   - creation, mis_a_jour

3. **wp_mbaa_evenement**

   - id_evenement (PK)
   - titre, descriptif
   - date_evenement, heure_debut, heure_fin
   - id_type_evenement (FK)
   - est_gratuit, prix, label_tarif
   - image_url, capacite_max
   - lieu_musee, public_cible, intervenant

4. **wp_mbaa_audioguide**

   - id_audioguide (PK)
   - id_oeuvre (FK)
   - fichier_audio_url
   - duree_secondes
   - langue, transcription

5. **wp_mbaa_exposition**
   - id_exposition (PK)
   - titre, description
   - date_debut, date_fin
   - statut, nombre_visiteurs
   - image_url

### Tables de référence

- **wp_mbaa_epoque** : Époques artistiques
- **wp_mbaa_salle** : Salles du musée
- **wp_mbaa_medium** : Techniques/médiums artistiques
- **wp_mbaa_mouvement_artistique** : Mouvements artistiques
- **wp_mbaa_categorie** : Catégories d'œuvres
- **wp_mbaa_type_evenement** : Types d'événements

### Tables de liaison

- **wp_mbaa_oeuvre_exposition** : Relation many-to-many entre œuvres et expositions

## Utilisation

### Accéder à l'interface d'administration

Après activation, un nouveau menu "Gestion Musée" apparaît dans le menu WordPress avec les sous-menus :

- Tableau de bord
- Artistes
- Œuvres
- Événements
- Audioguides
- Expositions
- Paramètres

### Ajouter un artiste

1. Aller dans "Gestion Musée > Artistes"
2. Cliquer sur "Ajouter un artiste"
3. Remplir le formulaire
4. Cliquer sur "Ajouter"

### Ajouter une œuvre

1. Aller dans "Gestion Musée > Œuvres"
2. Cliquer sur "Ajouter une œuvre"
3. Sélectionner l'artiste
4. Remplir les informations
5. Associer à une époque, salle, mouvement, etc.
6. Cliquer sur "Ajouter"

### Ajouter un événement

1. Aller dans "Gestion Musée > Événements"
2. Cliquer sur "Créer un événement"
3. Remplir les informations (titre, date, heure, type)
4. Définir le tarif (gratuit ou payant)
5. Cliquer sur "Ajouter"

## API / Fonctions utiles

### Récupérer des données

```php
// Récupérer tous les artistes
$artiste_manager = new MBAA_Artiste();
$artistes = $artiste_manager->get_all_artistes();

// Récupérer un artiste spécifique
$artiste = $artiste_manager->get_artiste($id);

// Récupérer toutes les œuvres
$oeuvre_manager = new MBAA_Oeuvre();
$oeuvres = $oeuvre_manager->get_all_oeuvres();

// Récupérer les œuvres pour la galerie publique
$oeuvres_galerie = $oeuvre_manager->get_galerie_oeuvres();

// Récupérer les événements à venir
$evenement_manager = new MBAA_Evenement();
$evenements = $evenement_manager->get_upcoming_evenements();
```

## Sécurité

- Toutes les entrées sont sanitizées avec les fonctions WordPress appropriées
- Utilisation de nonces pour les formulaires
- Prepared statements pour toutes les requêtes SQL
- Vérifications des capacités utilisateur (manage_options)

## Désinstallation

À la désactivation du plugin, les tables ne sont pas supprimées automatiquement pour préserver les données.

Pour supprimer complètement le plugin et ses données :

1. Désactiver le plugin
2. Supprimer le plugin via l'interface WordPress

Si vous souhaitez supprimer manuellement les tables :

```sql
DROP TABLE IF EXISTS wp_mbaa_oeuvre_exposition;
DROP TABLE IF EXISTS wp_mbaa_audioguide;
DROP TABLE IF EXISTS wp_mbaa_evenement;
DROP TABLE IF EXISTS wp_mbaa_oeuvre;
DROP TABLE IF EXISTS wp_mbaa_artiste;
DROP TABLE IF EXISTS wp_mbaa_exposition;
DROP TABLE IF EXISTS wp_mbaa_type_evenement;
DROP TABLE IF EXISTS wp_mbaa_categorie;
DROP TABLE IF EXISTS wp_mbaa_mouvement_artistique;
DROP TABLE IF EXISTS wp_mbaa_medium;
DROP TABLE IF EXISTS wp_mbaa_salle;
DROP TABLE IF EXISTS wp_mbaa_epoque;
```

## Support

Pour toute question ou problème, contactez : nina.tonnaire@exemple.com

## Changelog

### Version 2.0

- Refonte complète du plugin
- Utilisation de SQL pur au lieu de CPT
- Ajout du tableau de bord avec statistiques
- Amélioration de l'interface d'administration
- Ajout des audioguides
- Ajout des expositions

### Version 1.3

- Version initiale avec CPT UI

## Auteur

Nina Tonnaire - https://nina-tonnaire.com
