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
        $pdo->beginTransaction();
        
        // Supprimer les cours associés
        $stmt = $pdo->prepare("DELETE FROM cours WHERE matiere_id = ?");
        $stmt->execute([$id]);
        
        // Supprimer la matière
        $stmt = $pdo->prepare("DELETE FROM matieres WHERE id = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
        $_SESSION['success'] = "Matière supprimée avec succès";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression: " . $e->getMessage();
    }
}

header('Location: liste.php');
exit;
