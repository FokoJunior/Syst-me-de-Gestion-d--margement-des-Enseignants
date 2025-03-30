<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    try {
        // Vérifier si l'enseignant existe
        $stmt = $pdo->prepare("SELECT * FROM enseignants WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            // Supprimer d'abord les cours associés
            $stmt = $pdo->prepare("DELETE FROM cours WHERE enseignant_id = ?");
            $stmt->execute([$id]);
            
            // Supprimer l'enseignant
            $stmt = $pdo->prepare("DELETE FROM enseignants WHERE id = ?");
            $stmt->execute([$id]);
            
            $_SESSION['success'] = "Enseignant supprimé avec succès";
        } else {
            $_SESSION['error'] = "Enseignant non trouvé";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la suppression: " . $e->getMessage();
    }
}

header('Location: liste.php');
exit;
