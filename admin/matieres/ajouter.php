<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $code = $_POST['code'];
    $description = $_POST['description'];
    $coefficient = $_POST['coefficient'];
    $volume_horaire = $_POST['volume_horaire'];
    $filiere_id = $_POST['filiere_id'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO matieres (nom, code, description, coefficient, volume_horaire, filiere_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $code, $description, $coefficient, $volume_horaire, $filiere_id]);
        $_SESSION['success'] = "Matière ajoutée avec succès";
        header('Location: liste.php');
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de l'ajout: " . $e->getMessage();
    }
}

// Récupération de toutes les filieres avec leur nombre de matières
$filieres = $pdo->query("
    SELECT f.*, COUNT(m.id) as nb_matieres 
    FROM filieres f 
    LEFT JOIN matieres m ON f.id = m.filiere_id 
    GROUP BY f.id 
    ORDER BY f.nom
")->fetchAll();

// Récupération de la filière si passée en paramètre
$filiere_id = $_GET['filiere_id'] ?? null;
$filiere = null;
if ($filiere_id) {
    $stmt = $pdo->prepare("SELECT * FROM filieres WHERE id = ?");
    $stmt->execute([$filiere_id]);
    $filiere = $stmt->fetch();
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
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><i class='bx bx-book-add me-2'></i>Ajouter une matière</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom de la matière</label>
                                        <input type="text" class="form-control" name="nom" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" name="code" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Coefficient</label>
                                        <input type="number" class="form-control" name="coefficient" step="0.5" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Volume horaire</label>
                                        <input type="number" class="form-control" name="volume_horaire" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Filière</label>
                                    <select class="form-select" name="filiere_id" required <?= $filiere ? 'disabled' : '' ?>>
                                        <option value="">Sélectionner une filière</option>
                                        <?php foreach($filieres as $f): ?>
                                            <option value="<?= $f['id'] ?>" <?= ($filiere && $f['id'] == $filiere['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($f['nom']) ?> (<?= $f['nb_matieres'] ?> matières)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($filiere): ?>
                                        <input type="hidden" name="filiere_id" value="<?= $filiere['id'] ?>">
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class='bx bx-save me-2'></i>Enregistrer
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
