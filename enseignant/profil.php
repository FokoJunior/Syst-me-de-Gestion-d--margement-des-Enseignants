<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['enseignant'])) {
    header('Location: ../login.php');
    exit;
}

$enseignant_id = $_SESSION['enseignant']['id'];
$stmt = $pdo->prepare("SELECT * FROM enseignants WHERE id = ?");
$stmt->execute([$enseignant_id]);
$enseignant = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $specialite = $_POST['specialite'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    try {
        if (!empty($current_password)) {
            // Si changement de mot de passe
            if (password_verify($current_password, $enseignant['mot_de_passe'])) {
                if (!empty($new_password)) {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        UPDATE enseignants 
                        SET nom = ?, prenom = ?, email = ?, specialite = ?, mot_de_passe = ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$nom, $prenom, $email, $specialite, $password_hash, $enseignant_id]);
                }
            } else {
                throw new Exception("Mot de passe actuel incorrect");
            }
        } else {
            // Mise à jour sans mot de passe
            $stmt = $pdo->prepare("
                UPDATE enseignants 
                SET nom = ?, prenom = ?, email = ?, specialite = ? 
                WHERE id = ?
            ");
            $stmt->execute([$nom, $prenom, $email, $specialite, $enseignant_id]);
        }
        
        // Mise à jour des données de session
        $_SESSION['enseignant'] = array_merge($_SESSION['enseignant'], [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'specialite' => $specialite
        ]);
        
        $_SESSION['success'] = "Profil mis à jour avec succès";
        header('Location: profil.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupérer les statistiques de l'enseignant
$stats = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT c.id) as total_cours,
        COUNT(DISTINCT CASE WHEN c.emarge = 1 THEN c.id END) as cours_emerges,
        COUNT(DISTINCT m.id) as total_matieres,
        COUNT(DISTINCT f.id) as total_filieres
    FROM cours c
    LEFT JOIN matieres m ON c.matiere_id = m.id
    LEFT JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ?
");
$stats->execute([$enseignant_id]);
$statistiques = $stats->fetch();

// Récupérer les derniers cours
$derniers_cours = $pdo->prepare("
    SELECT c.*, m.nom as matiere_nom, f.nom as filiere_nom
    FROM cours c
    JOIN matieres m ON c.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE c.enseignant_id = ?
    ORDER BY c.horaire DESC
    LIMIT 5
");
$derniers_cours->execute([$enseignant_id]);
?>

<?php require_once '../includes/navbar-enseignant.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar-enseignant.php'; ?>
    
    <div class="main-content">
        <div class="header-card mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="text-white mb-2"><?= htmlspecialchars($enseignant['prenom'] . ' ' . $enseignant['nom']) ?></h2>
                    <p class="text-white-50 mb-0">
                        <i class='bx bx-briefcase me-2'></i><?= htmlspecialchars($enseignant['specialite']) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Infos personnelles -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" name="nom" 
                                       value="<?= htmlspecialchars($enseignant['nom']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="prenom" 
                                       value="<?= htmlspecialchars($enseignant['prenom']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?= htmlspecialchars($enseignant['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Spécialité</label>
                                <input type="text" class="form-control" name="specialite" 
                                       value="<?= htmlspecialchars($enseignant['specialite']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" class="form-control" name="current_password">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" name="new_password">
                            </div>

                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class='bx bx-save me-2'></i>Enregistrer les modifications
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistiques et activités -->
            <div class="col-lg-8">
                <!-- Cartes statistiques -->
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="stat-card gradient-1">
                            <div class="stat-icon"><i class='bx bx-calendar-check'></i></div>
                            <div class="stat-content">
                                <h3><?= $statistiques['total_cours'] ?></h3>
                                <p>Total cours</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card gradient-2">
                            <div class="stat-icon"><i class='bx bx-check-circle'></i></div>
                            <div class="stat-content">
                                <h3><?= $statistiques['cours_emerges'] ?></h3>
                                <p>Cours émargés</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Derniers cours -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Derniers cours</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Matière</th>
                                        <th>Filière</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($cours = $derniers_cours->fetch()): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($cours['horaire'])) ?></td>
                                        <td><?= htmlspecialchars($cours['matiere_nom']) ?></td>
                                        <td><?= htmlspecialchars($cours['filiere_nom']) ?></td>
                                        <td>
                                            <?php if($cours['emarge']): ?>
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

<?php require_once '../includes/footer.php'; ?>
