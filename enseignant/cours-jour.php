<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['enseignant'])) {
    header('Location: ../login.php');
    exit;
}

$enseignant_id = $_SESSION['enseignant']['id'];

// Récupération des cours du jour
$stmt = $pdo->prepare("
    SELECT c.*, 
           m.nom as matiere_nom, m.code as matiere_code,
           f.nom as filiere_nom,
           TIMESTAMPDIFF(MINUTE, NOW(), c.horaire) as minutes_before
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ? 
    AND DATE(c.horaire) = CURRENT_DATE
    ORDER BY c.horaire ASC
");
$stmt->execute([$enseignant_id]);
$cours_jour = $stmt->fetchAll();
?>

<?php require_once '../includes/navbar-enseignant.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar-enseignant.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="text-white mb-0">Cours du <?= date('d/m/Y') ?></h2>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (count($cours_jour) > 0): ?>
                    <div class="timeline">
                        <?php foreach($cours_jour as $cours): ?>
                            <div class="timeline-item">
                                <div class="timeline-time">
                                    <?= date('H:i', strtotime($cours['horaire'])) ?>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5><?= htmlspecialchars($cours['matiere_nom']) ?></h5>
                                            <p class="text-muted mb-0">
                                                <?= htmlspecialchars($cours['filiere_nom']) ?> | 
                                                Salle: <?= htmlspecialchars($cours['salle']) ?>
                                            </p>
                                        </div>
                                        <div>
                                            <?php if($cours['emarge']): ?>
                                                <span class="badge bg-success">Émargé</span>
                                            <?php else: ?>
                                                <?php if($cours['minutes_before'] <= 30 && $cours['minutes_before'] > -120): ?>
                                                    <a href="emarger.php?id=<?= $cours['id'] ?>" 
                                                       onclick="return confirm('Êtes-vous sûr de vouloir émarger ce cours ?')"
                                                       class="btn btn-sm btn-primary">
                                                        <i class='bx bx-check me-1'></i>Émarger
                                                    </a>
                                                <?php elseif($cours['minutes_before'] > 30): ?>
                                                    <span class="badge bg-secondary">À venir</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Non émargé</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class='bx bx-calendar-x text-muted' style="font-size: 4rem;"></i>
                        <p class="mt-3 text-muted">Aucun cours prévu aujourd'hui</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
