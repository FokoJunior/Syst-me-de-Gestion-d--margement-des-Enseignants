<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM cours WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        
        $_SESSION['success'] = "Cours supprimé avec succès";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la suppression: " . $e->getMessage();
    }
}

header('Location: liste.php');
exit;
