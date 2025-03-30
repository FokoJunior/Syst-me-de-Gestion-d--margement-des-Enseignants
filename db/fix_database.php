<?php
require_once '../config/db.php';

try {
    // Correction de la structure des tables
    $queries = [
        // Suppression des anciennes clés primaires
        "ALTER TABLE enseignants DROP PRIMARY KEY",
        "ALTER TABLE cours DROP PRIMARY KEY",
        "ALTER TABLE matieres DROP PRIMARY KEY", 
        "ALTER TABLE filieres DROP PRIMARY KEY",
        
        // Ajout des nouvelles clés primaires avec auto-increment
        "ALTER TABLE enseignants 
         MODIFY id int(11) NOT NULL AUTO_INCREMENT,
         ADD PRIMARY KEY (id)",
        
        "ALTER TABLE cours 
         MODIFY id int(11) NOT NULL AUTO_INCREMENT,
         ADD PRIMARY KEY (id)",
        
        "ALTER TABLE matieres 
         MODIFY id int(11) NOT NULL AUTO_INCREMENT,
         ADD PRIMARY KEY (id)",
        
        "ALTER TABLE filieres 
         MODIFY id int(11) NOT NULL AUTO_INCREMENT,
         ADD PRIMARY KEY (id)"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }
    
    echo "Structure de la base de données corrigée avec succès.";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
