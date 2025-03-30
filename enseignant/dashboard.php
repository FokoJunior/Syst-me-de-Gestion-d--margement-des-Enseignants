<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['enseignant'])) {
    header('Location: ../login.php');
    exit;
}

$enseignant_id = $_SESSION['enseignant']['id'];

// Statistiques complètes
$stats = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT c.id) as total_cours,
        COUNT(DISTINCT CASE WHEN c.emarge = 1 THEN c.id END) as cours_emerges,
        COUNT(DISTINCT CASE WHEN c.horaire >= CURRENT_DATE() THEN c.id END) as cours_a_venir,
        COUNT(DISTINCT m.id) as total_matieres,
        SUM(c.duree) as total_heures,
        COUNT(DISTINCT f.id) as total_filieres
    FROM cours c
    LEFT JOIN matieres m ON c.matiere_id = m.id
    LEFT JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ? AND c.horaire <= CURRENT_TIMESTAMP
");
$stats->execute([$enseignant_id]);
$statistiques = $stats->fetch();

// Améliorons la requête des prochains cours
$prochains_cours = $pdo->prepare("
    SELECT c.*, 
           m.nom as matiere_nom, 
           m.code as matiere_code,
           f.nom as filiere_nom,
           TIMESTAMPDIFF(MINUTE, NOW(), c.horaire) as minutes_before
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ? 
    AND c.horaire >= CURRENT_TIMESTAMP
    AND c.horaire <= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 7 DAY)
    ORDER BY c.horaire ASC 
    LIMIT 5
");
$prochains_cours->execute([$enseignant_id]);

// Derniers cours émargés
$derniers_cours = $pdo->prepare("
    SELECT c.*, 
           m.nom as matiere_nom, 
           f.nom as filiere_nom
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ? 
    AND c.emarge = 1
    ORDER BY c.horaire DESC 
    LIMIT 5
");
$derniers_cours->execute([$enseignant_id]);

// Remplacer l'utilisation de strftime par date en français
setlocale(LC_TIME, 'fr_FR.UTF8');
$jours = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
$mois = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

$date = new DateTime();
$jour = $jours[$date->format('w')];
$numero = $date->format('d');
$mois_actuel = $mois[(int)$date->format('m')];
$annee = $date->format('Y');
$date_fr = "$jour $numero $mois_actuel $annee";
?>

<?php require_once '../includes/navbar-enseignant.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar-enseignant.php'; ?>
    
    <div class="main-content">
        <!-- Entête avec profil rapide -->
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="text-white mb-2">Bonjour, <?= htmlspecialchars($_SESSION['enseignant']['prenom']) ?></h2>
                    <p class="text-white-50 mb-0">
                        <i class='bx bx-calendar me-2'></i>
                        <?= $date_fr ?>
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="profil.php" class="btn btn-light">
                        <i class='bx bx-user me-2'></i>Mon profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Cartes statistiques -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card gradient-1">
                    <div class="stat-icon"><i class='bx bx-calendar-check'></i></div>
                    <div class="stat-content">
                        <h3><?= $statistiques['cours_emerges'] ?></h3>
                        <p>Cours émargés</p>
                    </div>
                </div>
            </div>
            <!-- ... autres stats cards ... -->
        </div>

        <div class="row">
            <!-- Prochains cours -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class='bx bx-calendar me-2'></i>Mes prochains cours</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($prochains_cours->rowCount() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Matière</th>
                                            <th>Date/Heure</th>
                                            <th>Salle</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($cours = $prochains_cours->fetch()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?= htmlspecialchars($cours['matiere_nom']) ?></span>
                                                    <small class="text-muted"><?= htmlspecialchars($cours['filiere_nom']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($cours['horaire'])) ?>
                                                <?php if ($cours['minutes_before'] <= 30 && !$cours['emarge']): ?>
                                                    <span class="badge bg-warning">Bientôt</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($cours['salle']) ?></td>
                                            <td>
                                                <?php if($cours['emarge']): ?>
                                                    <span class="badge bg-success">Émargé</span>
                                                <?php else: ?>
                                                    <?php if($cours['minutes_before'] <= 30): ?>
                                                        <a href="emarger.php?id=<?= $cours['id'] ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class='bx bx-check me-1'></i>Émarger
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Pas encore disponible</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class='bx bx-calendar-x fs-1 text-muted'></i>
                                <p class="mb-0 mt-2">Aucun cours prévu pour les 7 prochains jours</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class='bx bx-history me-2'></i>Derniers cours émargés</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-feed">
                            <?php while($cours = $derniers_cours->fetch()): ?>
                                <div class="activity-item">
                                    <div class="icon bg-success">
                                        <i class='bx bx-check'></i>
                                    </div>
                                    <div class="content">
                                        <p class="message mb-1">
                                            <?= htmlspecialchars($cours['matiere_nom']) ?>
                                        </p>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($cours['horaire'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>