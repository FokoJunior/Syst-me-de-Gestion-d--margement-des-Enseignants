<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'];

// Récupération du cours
$stmt = $pdo->prepare("
    SELECT c.*, m.nom as matiere, e.nom as enseignant_nom, e.prenom as enseignant_prenom
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN enseignants e ON c.enseignant_id = e.id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$cours = $stmt->fetch();

if (!$cours) {
    header('Location: liste.php');
    exit;
}

// Listes des enseignants et matières
$enseignants = $pdo->query("SELECT id, nom, prenom FROM enseignants WHERE status = 'actif' ORDER BY nom")->fetchAll();
$matieres = $pdo->query("SELECT id, code, nom FROM matieres ORDER BY nom")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE cours 
            SET matiere_id = ?, enseignant_id = ?, horaire = ?, 
                salle = ?, type = ?, duree = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['matiere_id'],
            $_POST['enseignant_id'],
            $_POST['date'] . ' ' . $_POST['heure'],
            $_POST['salle'],
            $_POST['type'],
            $_POST['duree'],
            $_POST['status'],
            $id
        ]);
        
        $_SESSION['success'] = "Cours modifié avec succès";
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
                                <i class='bx bx-edit me-2'></i>Modifier le cours
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Matière</label>
                                        <select class="form-select" name="matiere_id" required>
                                            <?php foreach($matieres as $matiere): 
                                                $selected = $cours['matiere_id'] == $matiere['id'];
                                            ?>
                                                <optgroup label="<?= htmlspecialchars($matiere['filiere_nom']) ?>">
                                                    <option value="<?= $matiere['id'] ?>" <?= $selected ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($matiere['code'] . ' - ' . $matiere['nom']) ?>
                                                    </option>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Enseignant</label>
                                        <select class="form-select" name="enseignant_id" required>
                                            <?php foreach ($enseignants as $enseignant): ?>
                                                <option value="<?= $enseignant['id'] ?>" 
                                                        <?= $cours['enseignant_id'] == $enseignant['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($enseignant['prenom'] . ' ' . $enseignant['nom']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date</label>
                                        <input type="date" class="form-control" name="date" 
                                               value="<?= date('Y-m-d', strtotime($cours['horaire'])) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Heure</label>
                                        <input type="time" class="form-control" name="heure" 
                                               value="<?= date('H:i', strtotime($cours['horaire'])) ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Salle</label>
                                        <input type="text" class="form-control" name="salle" 
                                               value="<?= htmlspecialchars($cours['salle']) ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Type</label>
                                        <select class="form-select" name="type" required>
                                            <?php foreach(['CM', 'TD', 'TP'] as $type): ?>
                                                <option value="<?= $type ?>" <?= $cours['type'] == $type ? 'selected' : '' ?>>
                                                    <?= $type ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status" required>
                                            <?php foreach(['planifié', 'en_cours', 'terminé', 'annulé'] as $status): ?>
                                                <option value="<?= $status ?>" <?= $cours['status'] == $status ? 'selected' : '' ?>>
                                                    <?= ucfirst($status) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
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
