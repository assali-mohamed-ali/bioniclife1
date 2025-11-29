<?php
session_start();
require_once 'config/db.php';

// Only admin allowed
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    die('Accès non autorisé.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: boutique.php');
    exit;
}

$id = intval($_POST['id']);

// fetch product to remove image if exists
$stmt = $pdo->prepare('SELECT image_path FROM products WHERE id = ?');
$stmt->execute([$id]);
$prod = $stmt->fetch();
if (!$prod) {
    $_SESSION['admin_msg'] = 'Produit introuvable.';
    header('Location: boutique.php');
    exit;
}

// delete DB row
$del = $pdo->prepare('DELETE FROM products WHERE id = ?');
if ($del->execute([$id])) {
    // delete image file if present and inside images/products
    if (!empty($prod['image_path'])) {
        $path = __DIR__ . '/' . $prod['image_path'];
        if (file_exists($path)) @unlink($path);
    }
    $_SESSION['admin_msg'] = 'Produit supprimé.';
} else {
    $_SESSION['admin_msg'] = 'Erreur lors de la suppression.';
}

header('Location: boutique.php');
exit;
?>