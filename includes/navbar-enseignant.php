<nav class="navbar navbar-expand-lg navbar-glass fixed-top">
    <div class="container-fluid">
        <button class="btn btn-link d-lg-none" type="button" onclick="toggleSidebar()">
            <i class='bx bx-menu fs-4'></i>
        </button>

        <a class="navbar-brand" href="/gestion_enseignants/enseignant/dashboard.php">
            <i class='bx bxs-graduation me-2'></i>
            eMarge - Espace Enseignant
        </a>

        <div class="navbar-right d-flex align-items-center">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="d-none d-md-block">
                        <div class="fw-semibold"><?= $_SESSION['enseignant']['prenom'] ?></div>
                        <div class="text-muted small">Enseignant</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="/gestion_enseignants/enseignant/profil.php">
                            <i class='bx bx-user me-2'></i>Mon profil
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
