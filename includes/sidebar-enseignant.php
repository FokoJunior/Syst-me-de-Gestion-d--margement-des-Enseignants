<?php
$current_page = basename($_SERVER['PHP_SELF']);
$count = 0;

// Vérification de la disponibilité de PDO et de la session
if (isset($pdo) && isset($_SESSION['enseignant']['id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM cours 
            WHERE enseignant_id = ? 
            AND emarge = 0 
            AND horaire BETWEEN DATE_SUB(NOW(), INTERVAL 2 HOUR) 
            AND DATE_ADD(NOW(), INTERVAL 30 MINUTE)
        ");
        $stmt->execute([$_SESSION['enseignant']['id']]);
        $count = $stmt->fetchColumn();
    } catch(PDOException $e) {
        // Gérer silencieusement l'erreur
        $count = 0;
    }
}
?>

<div class="sidebar bg-dark text-white">
    <div class="sidebar-header p-3">
        <div class="d-flex align-items-center">
            <i class='bx bxs-user-circle fs-2 me-2'></i>
            <h5 class="mb-0">Espace Enseignant</h5>
        </div>
    </div>

    <div class="menu-category">Principal</div>
    <ul class="list-unstyled components">
        <li>
            <a href="/gestion_enseignants/enseignant/dashboard.php" 
               class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class='bx bxs-dashboard me-2'></i>
                <span>Tableau de bord</span>
            </a>
        </li>
    </ul>

    <div class="menu-category">Gestion des cours</div>
    <ul class="list-unstyled components">
        <li>
            <a href="/gestion_enseignants/enseignant/cours-jour.php" 
               class="nav-link text-white <?= $current_page == 'cours-jour.php' ? 'active' : '' ?>">
                <i class='bx bx-calendar-event me-2'></i>
                <span>Cours du jour</span>
            </a>
        </li>
        <li>
            <a href="/gestion_enseignants/enseignant/planning.php" 
               class="nav-link text-white <?= $current_page == 'planning.php' ? 'active' : '' ?>">
                <i class='bx bx-calendar me-2'></i>
                <span>Mon planning</span>
            </a>
        </li>
        <li>
            <a href="/gestion_enseignants/enseignant/cours-a-emarger.php" 
               class="nav-link text-white <?= $current_page == 'cours-a-emarger.php' ? 'active' : '' ?>">
                <i class='bx bx-check-square me-2'></i>
                <span>À émarger</span>
                <?php if ($count > 0): ?>
                <span class="badge bg-danger ms-auto"><?= $count ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>

    <div class="menu-category">Historique</div>
    <ul class="list-unstyled components">
        <li>
            <a href="/gestion_enseignants/enseignant/historique.php" 
               class="nav-link text-white <?= $current_page == 'historique.php' ? 'active' : '' ?>">
                <i class='bx bx-history me-2'></i>
                <span>Mes cours passés</span>
            </a>
        </li>
        <li>
            <a href="/gestion_enseignants/enseignant/statistiques.php" 
               class="nav-link text-white <?= $current_page == 'statistiques.php' ? 'active' : '' ?>">
                <i class='bx bx-bar-chart-alt-2 me-2'></i>
                <span>Mes statistiques</span>
            </a>
        </li>
    </ul>

    <div class="menu-category">Mon compte</div>
    <ul class="list-unstyled components">
        <li>
            <a href="/gestion_enseignants/enseignant/profil.php" 
               class="nav-link text-white <?= $current_page == 'profil.php' ? 'active' : '' ?>">
                <i class='bx bx-user me-2'></i>
                <span>Mon profil</span>
            </a>
        </li>
        <li>
            <a href="/gestion_enseignants/login.php?logout=1" class="nav-link text-white">
                <i class='bx bx-log-out me-2'></i>
                <span>Déconnexion</span>
            </a>
        </li>
    </ul>
</div>
