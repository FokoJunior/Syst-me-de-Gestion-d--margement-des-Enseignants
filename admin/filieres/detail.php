<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM filieres WHERE id = ?");
$stmt->execute([$id]);
$filiere = $stmt->fetch();

if (!$filiere) {
    header('Location: liste.php');
    exit;
}

// Récupération des matières de la filière
$matieres = $pdo->prepare("
    SELECT m.*, COUNT(c.id) as nombre_cours
    FROM matieres m
    LEFT JOIN cours c ON m.id = c.matiere_id
    WHERE m.filiere_id = ?
    GROUP BY m.id
    ORDER BY m.nom
");
$matieres->execute([$id]);

// Statistiques globales
$stats = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT m.id) as total_matieres,
        COUNT(DISTINCT c.id) as total_cours,
        COUNT(DISTINCT c.enseignant_id) as total_enseignants
    FROM matieres m
    LEFT JOIN cours c ON m.id = c.matiere_id
    WHERE m.filiere_id = ?
");
$stats->execute([$id]);
$statistiques = $stats->fetch();
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header-card">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="text-white mb-2"><?= htmlspecialchars($filiere['nom']) ?></h2>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="modifier.php?id=<?= $filiere['id'] ?>" class="btn btn-light">
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
                        <h3><?= $statistiques['total_matieres'] ?></h3>
                        <p>Matières</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card gradient-2">
                    <div class="stat-icon"><i class='bx bx-calendar'></i></div>
                    <div class="stat-content">
                        <h3><?= $statistiques['total_cours'] ?></h3>
                        <p>Cours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card gradient-3">
                    <div class="stat-icon"><i class='bx bx-user'></i></div>
                    <div class="stat-content">
                        <h3><?= $statistiques['total_enseignants'] ?></h3>
                        <p>Enseignants</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des matières -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des matières</h5>
                <a href="../matieres/ajouter.php?filiere_id=<?= $id ?>" class="btn btn-sm btn-primary">
                    <i class='bx bx-plus me-1'></i>Ajouter une matière
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Coefficient</th>
                                <th>Volume horaire</th>
                                <th>Cours prévus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($matiere = $matieres->fetch()): ?>
                            <tr>
                                <td><?= htmlspecialchars($matiere['code']) ?></td>
                                <td><?= htmlspecialchars($matiere['nom']) ?></td>
                                <td><?= $matiere['coefficient'] ?></td>
                                <td><?= $matiere['volume_horaire'] ?> h</td>
                                <td><?= $matiere['nombre_cours'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
