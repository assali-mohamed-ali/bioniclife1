<?php
session_start();
require_once 'config/db.php';

// Only admin allowed
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    die('Accès non autorisé.');
}

$message = '';

// id param required
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: boutique.php');
    exit;
}

// fetch product
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: boutique.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    // handle optional new image
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/images/products';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $file = $_FILES['image'];
        $allowed = ['image/jpeg','image/png','image/gif'];
        if ($file['size'] > 4 * 1024 * 1024) {
            $message = 'Image trop volumineuse (max 4MB).';
        } elseif (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
            $message = 'Type d\'image non autorisé.';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('p_', true) . '.' . $ext;
            $target = $uploadDir . '/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                // remove old image
                if (!empty($product['image_path'])) {
                    $old = __DIR__ . '/' . $product['image_path'];
                    if (file_exists($old)) @unlink($old);
                }
                $image_path = 'images/products/' . $filename;
            } else {
                $message = 'Erreur lors de l\'upload de l\'image.';
            }
        }
    } else {
        $image_path = $product['image_path'];
    }

    if ($message === '') {
        $stmt = $pdo->prepare('UPDATE products SET name = ?, description = ?, price = ?, image_path = ? WHERE id = ?');
        if ($stmt->execute([$name, $description, $price, $image_path, $id])) {
            $message = 'Produit mis à jour avec succès.';
            // refresh product data
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
            $stmt->execute([$id]);
            $product = $stmt->fetch();
        } else {
            $message = 'Erreur lors de la mise à jour.';
        }
    }
}

?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Modifier produit</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include __DIR__ . '/config/header.php'; ?>

<div class="auth-container" style="max-width:900px;">
    <h1 class="auth-header">Modifier le produit</h1>
    <?php if ($message): ?>
        <div class="message <?= (strpos($message,'succès')!==false ? 'success' : 'error') ?>"><?=htmlspecialchars($message)?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="auth-form">
        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="name" value="<?=htmlspecialchars($product['name'])?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required><?=htmlspecialchars($product['description'])?></textarea>
        </div>
        <div class="form-group">
            <label>Prix</label>
            <input type="number" step="0.01" name="price" value="<?=htmlspecialchars($product['price'])?>" required>
        </div>
        <div class="form-group">
            <label>Image actuelle</label>
            <?php if (!empty($product['image_path']) && file_exists(__DIR__.'/'.$product['image_path'])): ?>
                <img src="<?=htmlspecialchars($product['image_path'])?>" alt="image" style="max-width:200px; display:block; margin-bottom:8px;"/>
            <?php else: ?>
                <div style="color:#777;">(aucune image)</div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Nouvelle image (optionnel)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="boutique.php" class="btn-secondary" style="display:inline-block; margin-left:8px;">Annuler</a>
        </div>
    </form>
</div>
</body>
</html>