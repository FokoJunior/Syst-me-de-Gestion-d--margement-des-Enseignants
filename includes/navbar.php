<?php
// Déterminer le titre de la page actuelle
$current_page = basename($_SERVER['PHP_SELF']);
$page_titles = [
    'dashboard.php' => 'Tableau de bord',
    'liste.php' => 'Liste',
    'ajouter.php' => 'Ajouter',
    'detail.php' => 'Détail',
    'modifier.php' => 'Modifier',
];

// Déterminer le contexte (enseignants, matières, etc.)
$context = '';
if (strpos($_SERVER['REQUEST_URI'], '/enseignants/') !== false) {
    $context = 'Enseignants';
} elseif (strpos($_SERVER['REQUEST_URI'], '/matieres/') !== false) {
    $context = 'Matières';
} elseif (strpos($_SERVER['REQUEST_URI'], '/cours/') !== false) {
    $context = 'Cours';
}

$page_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] . ' ' . $context : 'Gestion des Enseignants';
?>

<nav class="navbar navbar-expand-lg navbar-glass fixed-top">
    <div class="container-fluid">
        <button class="btn btn-link d-lg-none" type="button" onclick="toggleSidebar()">
            <i class='bx bx-menu fs-4'></i>
        </button>
        <!-- Logo et Bouton Toggle -->
        <div class="d-flex align-items-center">
            <button class="btn btn-link sidebar-toggler d-lg-none me-3">
                <i class='bx bx-menu fs-4'></i>
            </button>
            <a class="navbar-brand" href="/gestion_enseignants/admin/dashboard.php">
                <i class='bx bxs-graduation me-2'></i>
                <?= $page_title ?>
            </a>
        </div>

        <!-- Actions et Profil -->
        <div class="navbar-right d-flex align-items-center">
            <div class="nav-item dropdown me-3">
                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
                    <i class='bx bx-plus-circle fs-4'></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <h6 class="dropdown-header">Actions rapides</h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/gestion_enseignants/admin/enseignants/ajouter.php">
                        <i class='bx bx-user-plus me-2'></i>
                        Nouvel enseignant
                    </a>
                    <a class="dropdown-item" href="/gestion_enseignants/admin/matieres/ajouter.php">
                        <i class='bx bx-book-add me-2'></i>
                        Nouvelle matière
                    </a>
                </div>
            </div>

            <!-- Profil -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <!-- <div class="avatar me-2">
                        <img src="/gestion_enseignants/assets/images/default-avatar.png" 
                             alt="Photo de profil" 
                             class="rounded-circle"
                             width="32">
                    </div> -->
                    <div class="d-none d-md-block">
                        <div class="fw-semibold"><?= $_SESSION['admin']['prenom'] ?? 'Admin' ?></div>
                        <div class="text-muted small">Administrateur</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="/gestion_enseignants/admin/profil.php">
                            <i class='bx bx-user me-2'></i>Mon profil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/gestion_enseignants/admin/parametres.php">
                            <i class='bx bx-cog me-2'></i>Paramètres
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="/gestion_enseignants/login.php?logout=1">
                            <i class='bx bx-log-out me-2'></i>Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar-glass {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}
.navbar{
    margin-bottom: 2rem !important;
    padding: 0.5rem 1rem !important;
}

.notification-dropdown {
    min-width: 280px;
    padding: 0;
}

.navbar .dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 0.5rem;
}

.navbar .dropdown-header {
    padding: 0.75rem 1rem;
    font-weight: 600;
    background: #f8f9fa;
}

.navbar .dropdown-item {
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
}

.navbar .dropdown-item:hover {
    background: #f8f9fa;
}

.avatar img {
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>