<?php
session_start();
require_once '../config/db.php';

// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {
    $cours_id = intval($_GET['id']);
    $enseignant_id = $_SESSION['enseignant']['id'];

    // Mise à jour directe
    $sql = "UPDATE cours SET emarge = 1 WHERE id = ? AND enseignant_id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$cours_id, $enseignant_id]);
        
        if ($result) {
            $_SESSION['success'] = "Cours émargé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de l'émargement";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur SQL : " . $e->getMessage();
    }
}

// Redirection
header('Location: cours-a-emarger.php');
exit();
?>