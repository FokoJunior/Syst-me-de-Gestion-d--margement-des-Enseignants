<?php
session_start();
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

// Récupération des statistiques
$stats = [
    'enseignants' => $pdo->query("SELECT COUNT(*) FROM enseignants")->fetchColumn(),
    'cours' => $pdo->query("SELECT COUNT(*) FROM cours")->fetchColumn(),
    'matieres' => $pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn(),
    'cours_today' => $pdo->query("SELECT COUNT(*) FROM cours WHERE DATE(horaire) = CURDATE()")->fetchColumn(),
    'cours_month' => $pdo->query("SELECT COUNT(*) FROM cours WHERE MONTH(horaire) = MONTH(CURRENT_DATE())")->fetchColumn(),
    'taux_presence' => $pdo->query("SELECT (COUNT(CASE WHEN emarge = 1 THEN 1 END) * 100 / COUNT(*)) FROM cours WHERE horaire < NOW()")->fetchColumn()
];
?>

<?php require_once '../includes/navbar.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- En-tête du dashboard -->
        <div class="header-card animate__animated animate__fadeIn">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="mb-2">Bienvenue, <?= htmlspecialchars($_SESSION['admin']['prenom']) ?></h1>
                    <p class="text-white-50 mb-4">Voici une vue d'ensemble de votre établissement</p>
                    <div class="quick-stats d-flex gap-4">
                        <div class="quick-stat">
                            <h3 class="mb-0"><?= $stats['cours_today'] ?></h3>
                            <small>Cours aujourd'hui</small>
                        </div>
                        <div class="quick-stat">
                            <h3 class="mb-0"><?= number_format($stats['taux_presence'], 1) ?>%</h3>
                            <small>Taux de présence</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="header-actions">
                        <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                            <i class='bx bx-user-plus'></i> Nouvel Enseignant
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                            <i class='bx bx-calendar-plus'></i> Nouveau Cours
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes statistiques -->
        <div class="row g-4 mb-4">
            <!-- Statistiques principales -->
            <div class="col-sm-6 col-xl-3 animate__animated animate__fadeInUp">
                <div class="stat-card gradient-1">
                    <div class="stat-icon"><i class='bx bxs-group'></i></div>
                    <div class="stat-content">
                        <h3 class="stat-count"><?= $stats['enseignants'] ?></h3>
                        <p class="stat-title">Enseignants</p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <div class="stat-card gradient-2">
                    <div class="stat-icon"><i class='bx bxs-calendar-check'></i></div>
                    <div class="stat-content">
                        <h3 class="stat-count"><?= $stats['cours'] ?></h3>
                        <p class="stat-title">Cours totaux</p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="stat-card gradient-3">
                    <div class="stat-icon"><i class='bx bxs-book'></i></div>
                    <div class="stat-content">
                        <h3 class="stat-count"><?= $stats['matieres'] ?></h3>
                        <p class="stat-title">Matières</p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                <div class="stat-card gradient-4">
                    <div class="stat-icon"><i class='bx bxs-bar-chart-alt-2'></i></div>
                    <div class="stat-content">
                        <h3 class="stat-count"><?= $stats['cours_month'] ?></h3>
                        <p class="stat-title">Cours ce mois</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widgets principaux -->
        <div class="row g-4">
            <div class="col-12 col-xl-8 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class='bx bx-calendar me-2'></i>Cours récents</h5>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Voir tout</a></li>
                                <li><a class="dropdown-item" href="#">Exporter</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Matière</th>
                                        <th>Enseignant</th>
                                        <th>Date/Heure</th>
                                        <th>Salle</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("
                                        SELECT c.*, m.nom as matiere, e.nom as enseignant_nom, e.prenom as enseignant_prenom 
                                        FROM cours c
                                        JOIN matieres m ON c.matiere_id = m.id
                                        JOIN enseignants e ON c.enseignant_id = e.id
                                        ORDER BY c.horaire DESC LIMIT 5
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td>".htmlspecialchars($row['matiere'])."</td>";
                                        echo "<td>".htmlspecialchars($row['enseignant_prenom'])." ".htmlspecialchars($row['enseignant_nom'])."</td>";
                                        echo "<td>".date('d/m/Y H:i', strtotime($row['horaire']))."</td>";
                                        echo "<td>".htmlspecialchars($row['salle'])."</td>";
                                        echo "<td>".($row['emarge'] ? '<span class="badge bg-success">Emargé</span>' : '<span class="badge bg-warning">À faire</span>')."</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-xl-4">
                <div class="row g-4">
                    <!-- Widget Activités Récentes -->
                    <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><i class='bx bx-bell me-2'></i>Activités Récentes</h5>
                            </div>
                            <div class="activity-feed">
                                <!-- Activités dynamiques ici -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Widget Taux de Présence -->
                    <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><i class='bx bx-line-chart me-2'></i>Taux de Présence</h5>
                            </div>
                            <div class="card-body">
                                <div class="presence-chart">
                                    <!-- Graphique à insérer ici -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>