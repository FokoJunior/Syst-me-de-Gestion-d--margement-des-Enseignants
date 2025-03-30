<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

// Récupération des filières avec leurs statistiques
$stmt = $pdo->query("
    SELECT f.*, 
           COUNT(DISTINCT m.id) as nombre_matieres,
           COUNT(DISTINCT c.id) as nombre_cours,
           ROUND(AVG(CASE WHEN c.emarge = 1 THEN 100 ELSE 0 END), 1) as taux_presence
    FROM filieres f
    LEFT JOIN matieres m ON f.id = m.filiere_id
    LEFT JOIN cours c ON m.id = c.matiere_id
    GROUP BY f.id
    ORDER BY f.nom
");
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0"><i class='bx bx-library me-2'></i>Filières</h2>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="ajouter.php" class="btn btn-primary">
                        <i class='bx bx-plus-circle me-2'></i>Nouvelle filière
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Statistiques</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $stmt->fetch()): ?>
                        <tr>
                            <td>
                                <h6 class="mb-0"><?= htmlspecialchars($row['nom']) ?></h6>
                            </td>
                            <td>
                                <div class="d-flex gap-3">
                                    <span class="badge bg-info">
                                        <i class='bx bx-book me-1'></i>
                                        <?= $row['nombre_matieres'] ?> matières
                                    </span>
                                    <span class="badge bg-secondary">
                                        <i class='bx bx-calendar me-1'></i>
                                        <?= $row['nombre_cours'] ?> cours
                                    </span>
                                    <span class="badge bg-<?= $row['taux_presence'] >= 75 ? 'success' : ($row['taux_presence'] >= 50 ? 'warning' : 'danger') ?>">
                                        <i class='bx bx-check-circle me-1'></i>
                                        <?= $row['taux_presence'] ?>% présence
                                    </span>
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

<!-- Modal de confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette filière ? Cette action supprimera également tous les cours et matières associés.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" action="supprimer.php">
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
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php require_once '../../includes/footer.php'; ?>
