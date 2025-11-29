<?php
session_start();
require_once 'config/db.php';

// Ensure users table has a 'role' column (safe check & add)
try {
    $hasRole = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch();
    if (!$hasRole) {
        // add role column with default 'user'
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
    }
} catch (Exception $e) {
    // if table doesn't exist yet or DB differs, we skip silently — registration will still try to insert safe columns
}

// Check whether an admin account already exists (we only allow creating one admin via signup)
$adminExists = false;
try {
    $count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    $adminExists = ($count > 0);
} catch (Exception $e) {
    // if users table doesn't exist yet, we'll treat as no admin exists (allow first admin creation)
    $adminExists = false;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm'];
    // optional admin code from sign-up form
    $admin_code = trim($_POST['admin_code'] ?? '');

    // Vérifications
    if ($password !== $confirm) {
        $message = "Les mots de passe ne correspondent pas identiques.";
    } elseif (strlen($password) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $message = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Re-check just before insert to avoid a race where two people can sign up simultaneously.
            try {
                $countNow = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
                $adminExists = ($countNow > 0);
            } catch (Exception $e) {
                // ignore, keep previous state
            }

            // decide role: default 'user'. Allow creating an admin only when there is still no admin and the admin_code matches.
            $role = 'user';
            if (!$adminExists && !empty($admin_code) && isset($ADMIN_SECRET) && $admin_code === $ADMIN_SECRET) {
                $role = 'admin';
            } elseif (!empty($admin_code) && isset($ADMIN_SECRET) && $admin_code === $ADMIN_SECRET && $adminExists) {
                // Someone tried to use the admin code but an admin already exists – politely ignore and continue as user
                $message = 'Code admin valide mais un administrateur existe déjà — le compte sera créé en tant qu\'utilisateur.';
            }

            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$firstname, $lastname, $email, $phone, $hash, $role])) {
                $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                $success = true;
                // if we just created the admin account, mark it so the UI will update
                if ($role === 'admin') {
                    $adminExists = true;
                }
            } else {
                $message = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - BionicLife</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {max-width: 500px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);}
        .form-group {margin-bottom: 15px;}
        label {display: block; margin-bottom: 5px; font-weight: bold;}
        input {width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-size: 16px;}
        .btn {background: #1a5fb4; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-size: 18px;}
        .btn:hover {background: #0d47a1;}
        .message {padding: 15px; margin: 20px 0; border-radius: 8px; text-align: center;}
        .success {background: #d4edda; color: #155724;}
        .error {background: #f8d7da; color: #721c24;}
    </style>
</head>
<body>

<?php include __DIR__ . '/config/header.php'; ?>

<div class="auth-container">
        <h1 class="auth-header">Inscription</h1>

    <?php if ($message): ?>
        <div class="message <?= isset($success) ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($adminExists): ?>
        <div class="message" style="background:#fff3cd;color:#856404;border:1px solid #ffeeba;">Un compte administrateur existe déjà — l'inscription suivante ne peut pas créer d'admin.</div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="firstname" required>
        </div>
        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="lastname" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Téléphone (optionnel)</label>
            <input type="tel" name="phone">
        </div>
        <div class="form-group">
            <label>Mot de passe (min. 6 caractères)</label>
            <input type="password" name="password" required minlength="6">
        </div>
        <?php if (!$adminExists): ?>
            <div class="form-group">
                <label>Code admin (optionnel)</label>
                <input type="text" name="admin_code" placeholder="Si vous avez un code d'administration">
                <small style="color:#777;">Seul le premier compte administrateur peut être créé via ce formulaire. Laisser vide pour un compte utilisateur normal.</small>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label>Confirmer le mot de passe</label>
            <input type="password" name="confirm" required>
        </div>
        <button type="submit" class="btn">S'inscrire</button>
    </form>

    <p class="auth-help">
        Déjà un compte ? <a href="login.php">Se connecter</a>
    </p>
    <a href="index.php" class="auth-help">← Retour à l'accueil</a>
</div>

</body>
</html>