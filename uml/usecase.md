```mermaid
graph TB
    subgraph Système de Gestion des Enseignants
        %% Acteurs
        Admin((Administrateur))
        Enseignant((Enseignant))

        %% Cas d'utilisation Admin
        UC1[Gérer les enseignants]
        UC2[Gérer les cours]
        UC3[Gérer les matières]
        UC4[Gérer les filières]
        UC5[Gérer les notifications]
        UC6[Consulter les statistiques]

        %% Sous-cas pour la gestion
        UC1.1[Ajouter enseignant]
        UC1.2[Modifier enseignant]
        UC1.3[Supprimer enseignant]
        UC1.4[Consulter enseignant]

        %% Cas d'utilisation Enseignant
        UC7[Consulter emploi du temps]
        UC8[Émarger les cours]
        UC9[Voir les notifications]
        UC10[Modifier profil]

        %% Relations Admin
        Admin --> UC1
        Admin --> UC2
        Admin --> UC3
        Admin --> UC4
        Admin --> UC5
        Admin --> UC6

        %% Décomposition des cas
        UC1 --- UC1.1
        UC1 --- UC1.2
        UC1 --- UC1.3
        UC1 --- UC1.4

        %% Relations Enseignant
        Enseignant --> UC7
        Enseignant --> UC8
        Enseignant --> UC9
        Enseignant --> UC10

        %% Authentification requise
        Auth[S'authentifier]
        Admin -.-> Auth
        Enseignant -.-> Auth
    end

```

## Description des Cas d'Utilisation

### Administrateur

1. **Gestion des Enseignants**
   - Ajouter un nouvel enseignant
   - Modifier les informations d'un enseignant
   - Supprimer un enseignant
   - Consulter la liste des enseignants

2. **Gestion des Cours**
   - Planifier un nouveau cours
   - Modifier un cours existant
   - Supprimer un cours
   - Suivre l'émargement des cours

3. **Gestion des Matières**
   - Créer une nouvelle matière
   - Modifier une matière
   - Supprimer une matière
   - Affecter des matières aux filières

4. **Gestion des Filières**
   - Créer une nouvelle filière
   - Modifier une filière
   - Supprimer une filière

5. **Gestion des Notifications**
   - Envoyer des notifications
   - Gérer les notifications système

### Enseignant

1. **Emploi du temps**
   - Consulter son emploi du temps
   - Voir les détails des cours

2. **Émargement**
   - Émarger les cours effectués
   - Consulter l'historique des émargements

3. **Notifications**
   - Recevoir des notifications
   - Marquer comme lu/non lu

4. **Profil**
   - Consulter son profil
   - Modifier ses informations personnelles
   - Changer son mot de passe
```
