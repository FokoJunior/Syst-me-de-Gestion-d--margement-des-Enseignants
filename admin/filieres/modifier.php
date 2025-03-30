<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM filieres WHERE id = ?");
$stmt->execute([$id]);
$filiere = $stmt->fetch();

if (!$filiere) {
    header('Location: liste.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    
    try {
        $stmt = $pdo->prepare("UPDATE filieres SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $id]);
        
        $_SESSION['success'] = "Filière modifiée avec succès";
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
                            <h5 class="card-title">
                                <i class='bx bx-edit me-2'></i>Modifier la filière
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label class="form-label">Nom de la filière</label>
                                    <input type="text" class="form-control" name="nom" 
                                           value="<?= htmlspecialchars($filiere['nom']) ?>" required>
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
