<?php
session_start();
require_once 'config/db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Vérification admin
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['mot_de_passe'])) {
        $_SESSION['admin'] = $admin;
        header('Location: admin/dashboard.php');
        exit;
    }
    
    // Vérification enseignant
    $stmt = $pdo->prepare("SELECT * FROM enseignants WHERE email = ?");
    $stmt->execute([$email]);
    $enseignant = $stmt->fetch();
    
    if ($enseignant && password_verify($password, $enseignant['mot_de_passe'])) {
        $_SESSION['enseignant'] = $enseignant;
        header('Location: enseignant/dashboard.php');
        exit;
    }
    
    $error = "Email ou mot de passe incorrect";
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | e - Marge</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-wrapper">
        <div class="login-card animate__animated animate__fadeInDown">
            <div class="text-center mb-4">
                <div class="login-logo">
                    <i class='bx bxs-graduation'></i>
                </div>
                <h2 class="login-title">eMarge</h2>
                <p class="text-muted">Connectez-vous pour accéder à votre espace</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <i class='bx bx-error-circle me-2'></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group mb-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Votre email" required>
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Votre mot de passe" required>
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn btn-primary btn-block">
                    <i class='bx bx-log-in-circle me-2'></i>Connexion
                </button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>