<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['enseignant'])) {
    header('Location: ../login.php');
    exit;
}

$enseignant_id = $_SESSION['enseignant']['id'];

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Corriger la requête SQL avec LIMIT
    $stmt = $pdo->prepare("
        SELECT c.*, 
               m.nom as matiere_nom, 
               m.code as matiere_code,
               f.nom as filiere_nom
        FROM cours c
        JOIN matieres m ON c.matiere_id = m.id
        JOIN filieres f ON m.filiere_id = f.id
        WHERE c.enseignant_id = ?
        ORDER BY c.horaire DESC
        LIMIT ?, ?
    ");
    $stmt->bindValue(1, $enseignant_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Comptage total pour pagination
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM cours WHERE enseignant_id = ?
    ");
    $stmt->execute([$enseignant_id]);
    $total_cours = $stmt->fetchColumn();
    $total_pages = ceil($total_cours / $limit);

} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<?php require_once '../includes/navbar-enseignant.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar-enseignant.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="text-white mb-0">Historique des cours</h2>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (count($cours) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Date/Heure</th>
                                    <th>Matière</th>
                                    <th>Filière</th>
                                    <th>Salle</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cours as $row): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($row['horaire'])) ?></td>
                                        <td>
                                            <div>
                                                <span class="fw-bold"><?= htmlspecialchars($row['matiere_nom']) ?></span>
                                                <span class="badge bg-primary ms-2"><?= htmlspecialchars($row['matiere_code']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($row['filiere_nom']) ?></td>
                                        <td><?= htmlspecialchars($row['salle']) ?></td>
                                        <td><span class="badge bg-info"><?= $row['type'] ?></span></td>
                                        <td>
                                            <?php if ($row['emarge']): ?>
                                                <span class="badge bg-success">
                                                    <i class='bx bx-check me-1'></i>Émargé
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class='bx bx-x me-1'></i>Non émargé
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination améliorée -->
                    <?php if ($total_pages > 1): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <nav>
                                <ul class="pagination">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page-1 ?>">Précédent</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page+1 ?>">Suivant</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-5">
                        <i class='bx bx-calendar-x text-muted' style="font-size: 4rem;"></i>
                        <p class="mt-3 text-muted">Aucun historique de cours disponible</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
