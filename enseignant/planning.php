<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['enseignant'])) {
    header('Location: ../login.php');
    exit;
}

$enseignant_id = $_SESSION['enseignant']['id'];

// Récupération de la semaine (par défaut semaine courante)
$week = isset($_GET['week']) ? (int)$_GET['week'] : 0;
$date = new DateTime();
$date->modify(sprintf('%+d week', $week));
$start_week = clone $date->modify('monday this week');
$end_week = clone $start_week->modify('+6 days');

// Récupération des cours de la semaine
$stmt = $pdo->prepare("
    SELECT c.*, 
           m.nom as matiere_nom, 
           m.code as matiere_code,
           f.nom as filiere_nom
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ? 
    AND c.horaire BETWEEN ? AND ?
    ORDER BY c.horaire ASC
");
$stmt->execute([
    $enseignant_id,
    $start_week->format('Y-m-d 00:00:00'),
    $end_week->format('Y-m-d 23:59:59')
]);
$cours = $stmt->fetchAll();

// Organiser les cours par jour
$planning = [];
for($i = 0; $i < 7; $i++) {
    $day = (clone $start_week)->modify("+$i day")->format('Y-m-d');
    $planning[$day] = [];
}

foreach($cours as $c) {
    $day = date('Y-m-d', strtotime($c['horaire']));
    $planning[$day][] = $c;
}
?>

<?php require_once '../includes/navbar-enseignant.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar-enseignant.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="text-white mb-0">Planning hebdomadaire</h2>
                    <p class="text-white-50">
                        Semaine du <?= $start_week->format('d/m/Y') ?> 
                        au <?= $end_week->format('d/m/Y') ?>
                    </p>
                </div>
                <div class="col text-end">
                    <a href="?week=<?= $week-1 ?>" class="btn btn-light me-2">
                        <i class='bx bx-chevron-left'></i>Semaine précédente
                    </a>
                    
                    <a href="?week=<?= $week+1 ?>" class="btn btn-light me-2">
                        Semaine suivante<i class='bx bx-chevron-right ms-2'></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <?php foreach($planning as $date => $cours_jour): ?>
                                    <th class="text-center">
                                        <?= (new DateTime($date))->format('D d/m') ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($h = 8; $h <= 18; $h++): ?>
                                <tr>
                                    <td class="text-center"><?= sprintf('%02d:00', $h) ?></td>
                                    <?php foreach($planning as $date => $cours_jour): ?>
                                        <td>
                                            <?php 
                                            foreach($cours_jour as $cours) {
                                                $cours_heure = (int)date('H', strtotime($cours['horaire']));
                                                if($cours_heure == $h) {
                                                    echo '<div class="cours-item p-2 mb-2 rounded ';
                                                    echo $cours['emarge'] ? 'bg-success' : 'bg-warning';
                                                    echo '">';
                                                    echo '<strong>' . htmlspecialchars($cours['matiere_code']) . '</strong><br>';
                                                    echo '<small>' . htmlspecialchars($cours['salle']) . '</small>';
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
