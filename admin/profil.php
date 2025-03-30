<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

$admin_id = $_SESSION['admin']['id'];
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    try {
        if (!empty($current_password)) {
            // Vérifier l'ancien mot de passe
            if (password_verify($current_password, $admin['mot_de_passe'])) {
                if (!empty($new_password)) {
                    // Mettre à jour avec le nouveau mot de passe
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admins SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
                    $stmt->execute([$nom, $prenom, $email, $password_hash, $admin_id]);
                } else {
                    throw new Exception("Le nouveau mot de passe ne peut pas être vide");
                }
            } else {
                throw new Exception("Le mot de passe actuel est incorrect");
            }
        } else {
            // Mise à jour sans changement de mot de passe
            $stmt = $pdo->prepare("UPDATE admins SET nom = ?, prenom = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $email, $admin_id]);
        }
        
        $_SESSION['success'] = "Profil mis à jour avec succès";
        // Mettre à jour les données de session
        $_SESSION['admin']['nom'] = $nom;
        $_SESSION['admin']['prenom'] = $prenom;
        $_SESSION['admin']['email'] = $email;
        
        header("Location: profil.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php require_once '../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="header-card mb-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="text-white mb-0">Mon Profil</h2>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class='bx bx-error-circle me-2'></i><?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <i class='bx bx-check-circle me-2'></i><?= $_SESSION['success'] ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom</label>
                                        <input type="text" class="form-control" name="nom" 
                                               value="<?= htmlspecialchars($admin['nom']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prénom</label>
                                        <input type="text" class="form-control" name="prenom" 
                                               value="<?= htmlspecialchars($admin['prenom']) ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= htmlspecialchars($admin['email']) ?>" required>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Changer le mot de passe</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mot de passe actuel</label>
                                        <input type="password" class="form-control" name="current_password">
                                        <small class="text-muted">Laissez vide pour ne pas modifier le mot de passe</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" name="new_password">
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class='bx bx-save me-2'></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Activité récente -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class='bx bx-history me-2'></i>Activité récente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- On pourrait ajouter ici un historique des actions de l'admin -->
                                <div class="text-muted text-center">
                                    Fonctionnalité à venir
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
