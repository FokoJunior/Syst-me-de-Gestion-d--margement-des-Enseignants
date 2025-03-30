<?php
if (!file_exists('../config/db.php')) {
    die('Fichier de configuration manquant');
}

session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['enseignant'])) {
    header('Location: ../login.php');
    exit;
}

$enseignant_id = $_SESSION['enseignant']['id'];

// Modifier la requête pour inclure tous les cours non émargés
$stmt = $pdo->prepare("
    SELECT c.*, 
           m.nom as matiere_nom, 
           m.code as matiere_code,
           f.nom as filiere_nom,
           TIMESTAMPDIFF(MINUTE, c.horaire, NOW()) as minutes_passed,
           TIMESTAMPDIFF(MINUTE, NOW(), c.horaire) as minutes_before,
           CASE 
               WHEN c.horaire <= DATE_ADD(NOW(), INTERVAL 30 MINUTE) 
               AND c.horaire >= DATE_SUB(NOW(), INTERVAL 2 HOUR) THEN 'disponible'
               WHEN c.horaire > DATE_ADD(NOW(), INTERVAL 30 MINUTE) THEN 'futur'
               ELSE 'retard'
           END as status_emargement
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ? 
    AND c.emarge = 0
    ORDER BY c.horaire DESC
");
$stmt->execute([$enseignant_id]);
$cours = $stmt->fetchAll();
?>

<?php require_once '../includes/navbar-enseignant.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar-enseignant.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="text-white mb-0">Cours à émarger</h2>
                    <p class="text-white-50 mb-0">
                        Les cours disponibles pour émargement (30 minutes avant à 2 heures après)
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php foreach($cours as $c): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($c['matiere_nom']) ?></h5>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($c['filiere_nom']) ?></p>
                                </div>
                                <?php
                                switch($c['status_emargement']) {
                                    case 'disponible':
                                        echo '<span class="badge bg-success">Disponible</span>';
                                        break;
                                    case 'futur':
                                        echo '<span class="badge bg-info">À venir</span>';
                                        break;
                                    case 'retard':
                                        echo '<span class="badge bg-warning">En retard</span>';
                                        break;
                                }
                                ?>
                            </div>
                            <div class="mb-3">
                                <i class='bx bx-calendar me-2'></i><?= date('d/m/Y', strtotime($c['horaire'])) ?>
                                <i class='bx bx-time ms-3 me-2'></i><?= date('H:i', strtotime($c['horaire'])) ?>
                                <i class='bx bx-map ms-3 me-2'></i><?= htmlspecialchars($c['salle']) ?>
                            </div>
                            <a href="emarger.php?id=<?= $c['id'] ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir émarger ce cours ?')"
                               class="btn w-100 <?= $c['status_emargement'] === 'disponible' ? 'btn-primary' : 'btn-warning' ?>">
                                <i class='bx bx-check me-2'></i>
                                <?= $c['status_emargement'] === 'disponible' ? 'Émarger maintenant' : 'Émarger en retard' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($cours)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class='bx bx-check-circle text-success' style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Tout est à jour !</h4>
                            <p class="text-muted">Aucun cours à émarger</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
