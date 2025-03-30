<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

// Requête modifiée pour inclure les informations de la filière
$stmt = $pdo->query("
    SELECT c.*, 
           m.nom as matiere, m.code as code_matiere,
           f.nom as filiere_nom,
           e.nom as enseignant_nom, e.prenom as enseignant_prenom
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    LEFT JOIN filieres f ON m.filiere_id = f.id
    JOIN enseignants e ON c.enseignant_id = e.id
    ORDER BY c.horaire DESC
");

$cours = $stmt->fetchAll();
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0"><i class='bx bx-calendar-check me-2'></i>Gestion des Cours</h2>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="ajouter.php" class="btn btn-primary">
                        <i class='bx bx-plus-circle me-2'></i>Nouveau Cours
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Date/Heure</th>
                                <th>Matière</th>
                                <th>Enseignant</th>
                                <th>Salle</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cours as $row): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class='bx bx-calendar me-2'></i>
                                        <?= date('d/m/Y H:i', strtotime($row['horaire'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($row['filiere_nom'] ?? '') ?></span>
                                    <span class="badge bg-primary"><?= htmlspecialchars($row['code_matiere'] ?? '') ?></span>
                                    <?= htmlspecialchars($row['matiere'] ?? '') ?>
                                </td>
                                <td><?= htmlspecialchars(($row['enseignant_prenom'] ?? '') . ' ' . ($row['enseignant_nom'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($row['salle'] ?? '') ?></td>
                                <td><span class="badge bg-info"><?= $row['type'] ?? 'N/A' ?></span></td>
                                <td>
                                    <?php if($row['emarge']): ?>
                                        <span class="badge bg-success">Émargé</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">En attente</span>
                                    <?php endif; ?>
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
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
                Êtes-vous sûr de vouloir supprimer ce cours ?
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
