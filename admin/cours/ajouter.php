<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

// Récupération des matières avec leurs filières et le nombre de cours
$matieres = $pdo->query("
    SELECT m.*, f.nom as filiere_nom,
           COUNT(c.id) as nb_cours,
           COALESCE(SUM(c.duree), 0) as total_heures
    FROM matieres m 
    LEFT JOIN filieres f ON m.filiere_id = f.id 
    LEFT JOIN cours c ON m.id = c.matiere_id
    GROUP BY m.id
    ORDER BY f.nom, m.code
")->fetchAll();

$enseignants = $pdo->query("
    SELECT * FROM enseignants 
    WHERE status = 'actif' 
    ORDER BY nom, prenom
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matiere_id = $_POST['matiere_id'];
    $enseignant_id = $_POST['enseignant_id'];
    $date = $_POST['date'];
    $heure = $_POST['heure'];
    $horaire = date('Y-m-d H:i:s', strtotime("$date $heure"));
    $salle = $_POST['salle'];
    $type = $_POST['type'];
    $duree = $_POST['duree'];
    
    try {
        // Vérifier si l'horaire est disponible pour la salle
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cours WHERE salle = ? AND horaire = ?");
        $stmt->execute([$salle, $horaire]);
        $salle_occupee = $stmt->fetchColumn() > 0;
        
        if ($salle_occupee) {
            throw new Exception("Cette salle est déjà occupée à cet horaire");
        }

        // Vérifier si l'enseignant est disponible
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cours WHERE enseignant_id = ? AND horaire = ?");
        $stmt->execute([$enseignant_id, $horaire]);
        $enseignant_occupe = $stmt->fetchColumn() > 0;
        
        if ($enseignant_occupe) {
            throw new Exception("L'enseignant est déjà occupé à cet horaire");
        }

        // Ajouter le cours
        $stmt = $pdo->prepare("
            INSERT INTO cours (
                matiere_id, 
                enseignant_id, 
                horaire, 
                salle, 
                type, 
                duree, 
                status
            ) VALUES (?, ?, ?, ?, ?, ?, 'planifié')
        ");
        
        $stmt->execute([
            $matiere_id,
            $enseignant_id,
            $horaire,
            $salle,
            $type,
            $duree
        ]);
        
        $_SESSION['success'] = "Cours ajouté avec succès";
        header('Location: liste.php');
        exit;
    } catch (Exception $e) {
        $error = "Erreur lors de l'ajout: " . $e->getMessage();
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
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class='bx bx-calendar-plus me-2'></i>Planifier un nouveau cours
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <i class='bx bx-error-circle me-2'></i><?= $error ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Matière</label>
                                        <select class="form-select" name="matiere_id" required>
                                            <option value="">Sélectionner une matière</option>
                                            <?php 
                                            $current_filiere = '';
                                            foreach($matieres as $matiere): 
                                                if ($current_filiere != $matiere['filiere_nom']):
                                                    if ($current_filiere != '') echo '</optgroup>';
                                                    $current_filiere = $matiere['filiere_nom'];
                                                    echo '<optgroup label="'.htmlspecialchars($matiere['filiere_nom']).'">';
                                                endif;
                                            ?>
                                                <option value="<?= $matiere['id'] ?>">
                                                    <?= htmlspecialchars($matiere['code'] . ' - ' . $matiere['nom']) ?>
                                                    (<?= floor($matiere['total_heures']/60) ?>h/<?= $matiere['volume_horaire'] ?>h)
                                                </option>
                                            <?php 
                                            endforeach;
                                            if ($current_filiere != '') echo '</optgroup>';
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Enseignant</label>
                                        <select class="form-select" name="enseignant_id" required>
                                            <option value="">Sélectionner un enseignant</option>
                                            <?php foreach($enseignants as $enseignant): ?>
                                                <option value="<?= $enseignant['id'] ?>">
                                                    <?= htmlspecialchars($enseignant['prenom'] . ' ' . $enseignant['nom']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date</label>
                                        <input type="date" class="form-control" name="date" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Heure</label>
                                        <input type="time" class="form-control" name="heure" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Salle</label>
                                        <input type="text" class="form-control" name="salle" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Type</label>
                                        <select class="form-select" name="type" required>
                                            <option value="CM">CM</option>
                                            <option value="TD">TD</option>
                                            <option value="TP">TP</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Durée (minutes)</label>
                                        <input type="number" class="form-control" name="duree" value="120" required>
                                    </div>
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
