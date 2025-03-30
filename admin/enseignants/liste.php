<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

// Gestion de la suppression
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Récupération des enseignants avec leurs statistiques
$stmt = $pdo->query("
    SELECT e.*, 
           COUNT(DISTINCT c.id) as nombre_cours,
           COUNT(DISTINCT CASE WHEN c.emarge = 1 THEN c.id END) as cours_emerges
    FROM enseignants e
    LEFT JOIN cours c ON e.id = c.enseignant_id
    GROUP BY e.id
    ORDER BY e.nom, e.prenom
");
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class='bx bx-check-circle me-2'></i><?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="content-header d-flex justify-content-between align-items-center mb-4">
            <h2><i class='bx bxs-user-detail me-2'></i>Gestion des Enseignants</h2>
            <div class="header-actions">
                <a href="ajouter.php" class="btn btn-primary">
                    <i class='bx bx-user-plus me-2'></i>Nouvel Enseignant
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Spécialité</th>
                                <th>Statistiques</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch()): 
                                $taux_presence = $row['nombre_cours'] > 0 
                                    ? round(($row['cours_emerges'] / $row['nombre_cours']) * 100) 
                                    : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nom']) ?></td>
                                <td><?= htmlspecialchars($row['prenom']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['specialite']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <small class="text-muted d-block">Cours</small>
                                            <span class="fw-bold"><?= $row['nombre_cours'] ?></span>
                                        </div>
                                        <div class="me-3">
                                            <small class="text-muted d-block">Présence</small>
                                            <span class="badge bg-<?= $taux_presence >= 75 ? 'success' : ($taux_presence >= 50 ? 'warning' : 'danger') ?>">
                                                <?= $taux_presence ?>%
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                            <i class='bx bx-show'></i>
                                        </a>
                                        <a href="modifier.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class='bx bx-edit'></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmerSuppression(<?= $row['id'] ?>)">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cet enseignant ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" action="supprimer.php" class="d-inline">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmerSuppression(id) {
    document.getElementById('deleteId').value = id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once '../../includes/footer.php'; ?>
