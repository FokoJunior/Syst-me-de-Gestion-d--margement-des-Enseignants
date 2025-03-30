<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        $pdo->beginTransaction();
        
        // Suppression des cours liés aux matières de la filière
        $stmt = $pdo->prepare("
            DELETE cours FROM cours 
            INNER JOIN matieres ON cours.matiere_id = matieres.id 
            WHERE matieres.filiere_id = ?
        ");
        $stmt->execute([$id]);
        
        // Suppression des matières
        $stmt = $pdo->prepare("DELETE FROM matieres WHERE filiere_id = ?");
        $stmt->execute([$id]);
        
        // Suppression de la filière
        $stmt = $pdo->prepare("DELETE FROM filieres WHERE id = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
        $_SESSION['success'] = "Filière et données associées supprimées avec succès";
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

header('Location: liste.php');
exit;
