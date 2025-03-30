<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

$stmt = $pdo->query("
    SELECT m.*, 
           COUNT(DISTINCT c.id) as nombre_cours,
           COUNT(DISTINCT c.enseignant_id) as nombre_enseignants
    FROM matieres m
    LEFT JOIN cours c ON m.id = c.matiere_id
    GROUP BY m.id
    ORDER BY m.nom
");
?>

<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0"><i class='bx bx-book me-2'></i>Matières</h2>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="ajouter.php" class="btn btn-primary">
                        <i class='bx bx-plus-circle me-2'></i>Nouvelle matière
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
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Volume Horaire</th>
                                <th>Coefficient</th>
                                <th>Statistiques</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch()): ?>
                            <tr>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($row['code']) ?></span></td>
                                <td><?= htmlspecialchars($row['nom']) ?></td>
                                <td><?= $row['volume_horaire'] ?> h</td>
                                <td><?= $row['coefficient'] ?></td>
                                <td>
                                    <div class="d-flex gap-3">
                                        <small class="text-muted">
                                            <i class='bx bx-calendar'></i> <?= $row['nombre_cours'] ?> cours
                                        </small>
                                        <small class="text-muted">
                                            <i class='bx bx-user'></i> <?= $row['nombre_enseignants'] ?> enseignants
                                        </small>
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

<!-- Modal de confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <!-- ... existing modal code ... -->
</div>

<script>
function confirmerSuppression(id) {
    document.getElementById('deleteId').value = id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once '../../includes/footer.php'; ?>
