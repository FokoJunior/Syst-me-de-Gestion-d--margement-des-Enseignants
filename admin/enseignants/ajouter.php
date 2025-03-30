<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $specialite = $_POST['specialite'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO enseignants (nom, prenom, email, mot_de_passe, specialite) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $password, $specialite]);
        
        $_SESSION['success'] = "Enseignant ajouté avec succès";
        header('Location: liste.php');
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de l'ajout de l'enseignant: " . $e->getMessage();
    }
}
?>

<!-- Inclusion de la navbar -->
<?php require_once '../../includes/navbar.php'; ?>

<div class="d-flex wrapper">
    <!-- Inclusion de la sidebar -->
    <?php require_once '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <h2 class="mb-4">Ajouter un Enseignant</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="specialite" class="form-label">Spécialité</label>
                        <input type="text" class="form-control" id="specialite" name="specialite" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                    <a href="liste.php" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>