<?php
session_start();
require_once 'config/db.php';

// If the user is already logged in, send them to the home page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['firstname'] . ' ' . $user['lastname'];
        $_SESSION['user_role']  = $user['role'];
        // After successful login, redirect back to the public home page
        header("Location: index.php");
        exit;
    } else {
        $message = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - BionicLife</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {max-width: 400px; margin: 80px auto; padding: 40px; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);}
        /* même style que register.php */
        /* ... (copie le style du register.php pour que ça soit joli) */
        .btn {background:#1a5fb4; color:white; padding:15px; border:none; border-radius:8px; width:100%; font-size:18px; cursor:pointer;}
        .error {background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin:20px 0; text-align:center;}
    </style>
</head>
<body>

<?php include __DIR__ . '/config/header.php'; ?>

<div class="auth-container">
    <h1 class="auth-header">Connexion</h1>

    <?php if ($message): ?>
        <div class="error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Se connecter</button>
    </form>

    <p class="auth-help">
        Pas de compte ? <a href="register.php">Créer un compte</a>
    </p>
    <a href="index.php" class="auth-help">← Retour à l'accueil</a>
</div>

</body>
</html>