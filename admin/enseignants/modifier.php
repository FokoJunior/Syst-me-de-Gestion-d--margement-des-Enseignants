<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: liste.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM enseignants WHERE id = ?");
$stmt->execute([$id]);
$enseignant = $stmt->fetch();

if (!$enseignant) {
    header('Location: liste.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $specialite = $_POST['specialite'];
    
    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE enseignants SET nom = ?, prenom = ?, email = ?, specialite = ?, mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $email, $specialite, $password, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE enseignants SET nom = ?, prenom = ?, email = ?, specialite = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $email, $specialite, $id]);
        }
        
        $_SESSION['success'] = "Enseignant modifié avec succès";
        header('Location: liste.php');
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de la modification: " . $e->getMessage();
    }
}
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"><i class='bx bx-edit me-2'></i>Modifier l'enseignant</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($enseignant['nom']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($enseignant['prenom']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($enseignant['email']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas modifier)</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="specialite" class="form-label">Spécialité</label>
                                    <input type="text" class="form-control" id="specialite" name="specialite" value="<?= htmlspecialchars($enseignant['specialite']) ?>" required>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class='bx bx-save me-2'></i>Enregistrer les modifications
                                    </button>
                                    <a href="liste.php" class="btn btn-secondary">
                                        <i class='bx bx-arrow-back me-2'></i>Retour
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
