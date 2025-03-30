<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT c.*, 
           m.nom as matiere, m.code as code_matiere,
           e.nom as enseignant_nom, e.prenom as enseignant_prenom
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
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <!-- En-tête du cours -->
            <div class="header-card">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="text-white">
                            Cours de <?= htmlspecialchars($cours['matiere']) ?>
                        </h2>
                        <p class="text-white-50 mb-0">
                            <i class='bx bx-calendar me-2'></i>
                            <?= date('d/m/Y H:i', strtotime($cours['horaire'])) ?> 
                            | Salle <?= htmlspecialchars($cours['salle']) ?>
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button class="btn btn-light me-2" <?= $cours['emarge'] ? 'disabled' : '' ?>>
                            <i class='bx bx-check-square me-2'></i>Émarger
                        </button>
                        <a href="modifier.php?id=<?= $cours['id'] ?>" class="btn btn-light">
                            <i class='bx bx-edit me-2'></i>Modifier
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Détails du cours -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Détails du cours</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Type :</strong> <span class="badge bg-info"><?= $cours['type'] ?></span></p>
                                    <p><strong>Status :</strong> 
                                        <span class="badge bg-<?= $cours['status'] == 'terminé' ? 'success' : 
                                            ($cours['status'] == 'annulé' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($cours['status']) ?>
                                        </span>
                                    </p>
                                    <p><strong>Durée :</strong> <?= $cours['duree'] ?> minutes</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Matière :</strong> <?= htmlspecialchars($cours['code_matiere'] . ' - ' . $cours['matiere']) ?></p>
                                    <p><strong>Salle :</strong> <?= htmlspecialchars($cours['salle']) ?></p>
                                    <p><strong>Date de création :</strong> <?= date('d/m/Y', strtotime($cours['date_creation'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Informations sur l'enseignant -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Enseignant responsable</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class='bx bxs-user-circle' style="font-size: 3rem; color: var(--primary-color)"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0"><?= htmlspecialchars($cours['enseignant_prenom'] . ' ' . $cours['enseignant_nom']) ?></h6>
                                    <a href="../enseignants/detail.php?id=<?= $cours['enseignant_id'] ?>" class="btn btn-sm btn-light mt-2">
                                        <i class='bx bx-user me-1'></i>Voir le profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
