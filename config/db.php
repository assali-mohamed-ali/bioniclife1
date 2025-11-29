<?php
// config/db.php - Connexion à ta base brasbionique

$host = 'localhost';
$dbname = 'brasbionique';      // ← le nom exact de ta base
$username = 'root';            // généralement "root" en local
$password = '';                // mets ton mot de passe MySQL ici si tu en as un

// Secret code you can give to trusted people to create admin accounts at sign-up.
// Change this to a strong value or remove the admin-signup flow for production.
$ADMIN_SECRET = 'CHANGE_THIS_ADMIN_SECRET';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>