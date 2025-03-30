# eMarge - Système de Gestion d'Émargement des Enseignants

## 📝 Description détaillée

Application web complète de gestion d'émargement numérique permettant aux établissements d'enseignement de gérer et suivre les présences des enseignants. L'application fournit deux espaces distincts : un pour les enseignants et un pour l'administration.

## 🎯 Objectifs du projet

- Simplifier le processus d'émargement
- Réduire l'utilisation du papier
- Améliorer le suivi des présences
- Générer des statistiques précises
- Faciliter la gestion administrative

## 🚀 Fonctionnalités détaillées

### 👨‍🏫 Espace Enseignant

#### Dashboard

- Vue d'ensemble personnalisée
- Statistiques individuelles
- Prochains cours à venir
- Alertes d'émargement

#### Émargement

- Disponible 30 minutes avant le cours
- Reste accessible jusqu'à 2 heures après
- Confirmation requise
- Horodatage automatique

#### Planning

- Vue hebdomadaire interactive
- Navigation entre les semaines
- Filtres par matière/filière
- Code couleur par statut

#### Profil

- Gestion des informations personnelles
- Modification du mot de passe
- Historique des activités
- Statistiques personnelles

### 👨‍💼 Espace Administrateur

#### Gestion des Enseignants

- Ajout/Modification/Suppression
- Attribution des matières
- Suivi des présences
- Statistiques individuelles

#### Gestion des Matières

- Création et organisation
- Association aux filières
- Gestion des volumes horaires
- Attribution des coefficients

#### Gestion des Filières

- Création/Modification
- Association des matières
- Planning par filière
- Statistiques globales

#### Gestion des Cours

- Planification détaillée
- Attribution des salles
- Suivi en temps réel
- Historique complet

## 💾 Structure de la base de données

### Tables principales

1. **enseignants**

   - id (PK, AUTO_INCREMENT)
   - nom
   - prenom
   - email (UNIQUE)
   - mot_de_passe
   - specialite
   - status
   - date_creation
2. **matieres**

   - id (PK, AUTO_INCREMENT)
   - nom
   - code
   - description
   - coefficient
   - volume_horaire
   - filiere_id (FK)
3. **filieres**

   - id (PK, AUTO_INCREMENT)
   - nom
   - description
   - status
4. **cours**

   - id (PK, AUTO_INCREMENT)
   - matiere_id (FK)
   - enseignant_id (FK)
   - horaire
   - salle
   - emarge
   - date_emargement
   - type (CM/TD/TP)
   - status

## 🛠️ Installation détaillée

1. **Prérequis système**

```bash
PHP >= 8.2
MySQL >= 5.7
Apache >= 2.4
```

2. **Clonage du projet**

```bash
cd /opt/lampp/htdocs/
git clone https://github.com/votre-repo/gestion_enseignants.git
```

3. **Configuration de la base de données**

```bash
mysql -u root -p
CREATE DATABASE gestion_enseignants;
USE gestion_enseignants;
source gestion_enseignants.sql;
```

4. **Configuration PHP**

```php
// config/db.php
$host = 'localhost';
$dbname = 'gestion_enseignants';
$username = 'root';
$password = '';
```

## 📁 Structure du projet détaillée
