<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM enseignants WHERE id = ?");
$stmt->execute([$id]);
$enseignant = $stmt->fetch();

if (!$enseignant) {
    header('Location: liste.php');
    exit;
}

// Statistiques de l'enseignant
$stats = $pdo->prepare("
    SELECT 
        COUNT(*) as total_cours,
        COUNT(CASE WHEN emarge = 1 THEN 1 END) as cours_emerges,
        COUNT(DISTINCT matiere_id) as nb_matieres
    FROM cours 
    WHERE enseignant_id = ?
");
$stats->execute([$id]);
$statistiques = $stats->fetch();

// Statistiques des matières enseignées par filière
$matieres_par_filiere = $pdo->prepare("
    SELECT f.nom as filiere, COUNT(DISTINCT m.id) as nb_matieres
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ?
    GROUP BY f.id
");
$matieres_par_filiere->execute([$id]);

// Derniers cours
$cours = $pdo->prepare("
    SELECT c.*, m.nom as matiere 
    FROM cours c 
    JOIN matieres m ON c.matiere_id = m.id 
    WHERE c.enseignant_id = ? 
    ORDER BY c.horaire DESC 
    LIMIT 10
");
$cours->execute([$id]);
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Profil de l'enseignant -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar mb-3">
                                <i class='bx bxs-user-circle' style="font-size: 6rem; color: var(--primary-color)"></i>
                            </div>
                            <h4><?= htmlspecialchars($enseignant['prenom'] . ' ' . $enseignant['nom']) ?></h4>
                            <p class="text-muted"><?= htmlspecialchars($enseignant['specialite']) ?></p>
                            <p class="mb-2">
                                <i class='bx bx-envelope'></i> <?= htmlspecialchars($enseignant['email']) ?>
                            </p>
                            <div class="mt-4">
                                <a href="modifier.php?id=<?= $enseignant['id'] ?>" class="btn btn-primary">
                                    <i class='bx bx-edit'></i> Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques et Cours -->
                <div class="col-md-8">
                    <!-- Statistiques -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card gradient-1">
                                <div class="stat-icon"><i class='bx bx-calendar'></i></div>
                                <div class="stat-content">
                                    <h3><?= $statistiques['total_cours'] ?></h3>
                                    <p>Total Cours</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card gradient-2">
                                <div class="stat-icon"><i class='bx bx-check-circle'></i></div>
                                <div class="stat-content">
                                    <h3><?= $statistiques['cours_emerges'] ?></h3>
                                    <p>Cours Émargés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card gradient-3">
                                <div class="stat-icon"><i class='bx bx-book'></i></div>
                                <div class="stat-content">
                                    <h3><?= $statistiques['nb_matieres'] ?></h3>
                                    <p>Matières</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Répartition par filière -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Répartition par filière</h5>
                        </div>
                        <div class="card-body">
                            <?php while($row = $matieres_par_filiere->fetch()): ?>
                                <div class="mb-2">
                                    <span class="badge bg-info"><?= htmlspecialchars($row['filiere']) ?></span>
                                    <span><?= $row['nb_matieres'] ?> matières</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Liste des derniers cours -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class='bx bx-calendar me-2'></i>Derniers Cours</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Matière</th>
                                            <th>Date</th>
                                            <th>Salle</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $cours->fetch()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['matiere']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['horaire'])) ?></td>
                                            <td><?= htmlspecialchars($row['salle']) ?></td>
                                            <td>
                                                <?php if ($row['emarge']): ?>
                                                    <span class="badge bg-success">Émargé</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">En attente</span>
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
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
