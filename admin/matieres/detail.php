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

// Statistiques des cours
$stats = $pdo->prepare("
    SELECT 
        COUNT(*) as total_cours,
        COUNT(DISTINCT enseignant_id) as nb_enseignants,
        COUNT(CASE WHEN emarge = 1 THEN 1 END) as cours_effectues
    FROM cours 
    WHERE matiere_id = ?
");
$stats->execute([$id]);
$statistiques = $stats->fetch();

// Liste des enseignants de cette matière
$enseignants = $pdo->prepare("
    SELECT DISTINCT e.*, COUNT(c.id) as nb_cours
    FROM enseignants e
    JOIN cours c ON e.id = c.enseignant_id
    WHERE c.matiere_id = ?
    GROUP BY e.id
    ORDER BY nb_cours DESC
");
$enseignants->execute([$id]);

// Derniers cours
$cours = $pdo->prepare("
    SELECT c.*, e.nom as enseignant_nom, e.prenom as enseignant_prenom
    FROM cours c
    JOIN enseignants e ON c.enseignant_id = e.id
    WHERE c.matiere_id = ?
    ORDER BY c.horaire DESC
    LIMIT 5
");
$cours->execute([$id]);
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <!-- Entête de la matière -->
            <div class="header-card">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="text-white mb-2"><?= htmlspecialchars($matiere['code']) ?> - <?= htmlspecialchars($matiere['nom']) ?></h2>
                        <p class="text-white-50 mb-0"><?= htmlspecialchars($matiere['description']) ?></p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <a href="modifier.php?id=<?= $matiere['id'] ?>" class="btn btn-light">
                            <i class='bx bx-edit me-2'></i>Modifier
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card gradient-1">
                        <div class="stat-icon"><i class='bx bx-book'></i></div>
                        <div class="stat-content">
                            <h3><?= $statistiques['total_cours'] ?></h3>
                            <p>Total Cours</p>
                        </div>
                    </div>
                </div>
                <!-- ... autres stats ... -->
            </div>

            <!-- Liste des cours -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class='bx bx-calendar me-2'></i>Derniers cours</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Enseignant</th>
                                    <th>Salle</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $cours->fetch()): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['horaire'])) ?></td>
                                    <td><?= htmlspecialchars($row['enseignant_prenom'] . ' ' . $row['enseignant_nom']) ?></td>
                                    <td><?= htmlspecialchars($row['salle']) ?></td>
                                    <td>
                                        <?php if ($row['emarge']): ?>
                                            <span class="badge bg-success">Effectué</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">À venir</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
