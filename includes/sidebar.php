<?php
// Détecter la page active
$current_page = str_replace('/gestion_enseignants/admin/', '', $_SERVER['PHP_SELF']);
$current_section = explode('/', $current_page)[0];
?>

<div class="sidebar bg-dark text-white">
    <div class="sidebar-header p-3">
        <div class="d-flex align-items-center">
            <i class='bx bxs-graduation fs-2 me-2'></i>
            <h5 class="mb-0">eMarge</h5>
        </div>
    </div>

    <div class="menu-category">Principal</div>

    <ul class="list-unstyled components">
        <li>
            <a href="/gestion_enseignants/admin/dashboard.php" 
               class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class='bx bxs-dashboard me-2'></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="menu-category">Gestion</div>

        <li>
            <a href="#enseignantsSubmenu" data-bs-toggle="collapse" 
               class="nav-link text-white <?= $current_section == 'enseignants' ? 'active' : '' ?>">
                <i class='bx bxs-user-detail me-2'></i>
                <span>Enseignants</span>
                <i class='bx bx-chevron-down ms-auto'></i>
            </a>
            <ul class="collapse <?= $current_section == 'enseignants' ? 'show' : '' ?> list-unstyled" id="enseignantsSubmenu">
                <li>
                    <a href="/gestion_enseignants/admin/enseignants/liste.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'enseignants/liste.php' ? 'active' : '' ?>">
                        <i class='bx bx-list-ul me-2'></i>Liste
                    </a>
                </li>
                <li>
                    <a href="/gestion_enseignants/admin/enseignants/ajouter.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'enseignants/ajouter.php' ? 'active' : '' ?>">
                        <i class='bx bx-user-plus me-2'></i>Ajouter
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="#matieresSubmenu" data-bs-toggle="collapse" 
               class="nav-link text-white <?= $current_section == 'matieres' ? 'active' : '' ?>">
                <i class='bx bxs-book me-2'></i>
                <span>Matières</span>
                <i class='bx bx-chevron-down ms-auto'></i>
            </a>
            <ul class="collapse <?= $current_section == 'matieres' ? 'show' : '' ?> list-unstyled" id="matieresSubmenu">
                <li>
                    <a href="/gestion_enseignants/admin/matieres/liste.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'matieres/liste.php' ? 'active' : '' ?>">
                        <i class='bx bx-list-ul me-2'></i>Liste
                    </a>
                </li>
                <li>
                    <a href="/gestion_enseignants/admin/matieres/ajouter.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'matieres/ajouter.php' ? 'active' : '' ?>">
                        <i class='bx bx-plus-circle me-2'></i>Ajouter
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="#coursSubmenu" data-bs-toggle="collapse" 
               class="nav-link text-white <?= $current_section == 'cours' ? 'active' : '' ?>">
                <i class='bx bxs-calendar me-2'></i>
                <span>Cours</span>
                <i class='bx bx-chevron-down ms-auto'></i>
            </a>
            <ul class="collapse <?= $current_section == 'cours' ? 'show' : '' ?> list-unstyled" id="coursSubmenu">
                <li>
                    <a href="/gestion_enseignants/admin/cours/liste.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'cours/liste.php' ? 'active' : '' ?>">
                        <i class='bx bx-list-ul me-2'></i>Liste
                    </a>
                </li>
                <li>
                    <a href="/gestion_enseignants/admin/cours/ajouter.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'cours/ajouter.php' ? 'active' : '' ?>">
                        <i class='bx bx-plus-circle me-2'></i>Ajouter
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="#filieresSubmenu" data-bs-toggle="collapse" 
               class="nav-link text-white <?= $current_section == 'filieres' ? 'active' : '' ?>">
                <i class='bx bxs-calendar me-2'></i>
                <span>Filieres</span>
                <i class='bx bx-chevron-down ms-auto'></i>
            </a>
            <ul class="collapse <?= $current_section == 'filieres' ? 'show' : '' ?> list-unstyled" id="filieresSubmenu">
                <li>
                    <a href="/gestion_enseignants/admin/filieres/liste.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'filieres/liste.php' ? 'active' : '' ?>">
                        <i class='bx bx-list-ul me-2'></i>Liste
                    </a>
                </li>
                <li>
                    <a href="/gestion_enseignants/admin/filieres/ajouter.php" 
                       class="nav-link text-white ps-4 <?= $current_page == 'filieres/ajouter.php' ? 'active' : '' ?>">
                        <i class='bx bx-plus-circle me-2'></i>Ajouter
                    </a>
                </li>
            </ul>
        </li>

        <div class="menu-category">Configuration</div>

        <li>
            <a href="/gestion_enseignants/admin/profil.php" 
               class="nav-link text-white <?= $current_page == 'profil.php' ? 'active' : '' ?>">
                <i class='bx bx-cog me-2'></i>
                <span>Paramètres</span>
            </a>
        </li>
    </ul>
</div> 