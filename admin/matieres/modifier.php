<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM matieres WHERE id = ?");
$stmt->execute([$id]);
$matiere = $stmt->fetch();

if (!$matiere) {
    header('Location: liste.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $code = $_POST['code'];
    $description = $_POST['description'];
    $coefficient = $_POST['coefficient'];
    $volume_horaire = $_POST['volume_horaire'];
    
    try {
        $stmt = $pdo->prepare("UPDATE matieres SET nom = ?, code = ?, description = ?, coefficient = ?, volume_horaire = ? WHERE id = ?");
        $stmt->execute([$nom, $code, $description, $coefficient, $volume_horaire, $id]);
        
        $_SESSION['success'] = "Matière modifiée avec succès";
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
                            <h5><i class='bx bx-edit me-2'></i>Modifier la matière</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom de la matière</label>
                                        <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($matiere['nom']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" name="code" value="<?= htmlspecialchars($matiere['code']) ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Coefficient</label>
                                        <input type="number" class="form-control" name="coefficient" step="0.5" value="<?= $matiere['coefficient'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Volume horaire</label>
                                        <input type="number" class="form-control" name="volume_horaire" value="<?= $matiere['volume_horaire'] ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($matiere['description']) ?></textarea>
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
