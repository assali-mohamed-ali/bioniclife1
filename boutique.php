<?php
include __DIR__ . '/config/header.php';
// Need DB access for products
require_once __DIR__ . '/config/db.php';

// Ensure products table exists (simple migration)
$pdo->exec("CREATE TABLE IF NOT EXISTS products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  image_path VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Message for admin actions
$admin_msg = '';

// Handle product add (only admin allowed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $price = floatval($_POST['price'] ?? 0);

  // Handle image upload
  $image_path = null;
  if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/images/products';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $file = $_FILES['image'];
    // basic validation
    $allowed = ['image/jpeg','image/png','image/gif'];
    if ($file['size'] > 4 * 1024 * 1024) {
      $admin_msg = 'Image trop volumineuse (max 4MB).';
    } elseif (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
      $admin_msg = 'Type d\'image non autorisé.';
    } else {
      $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
      $filename = uniqid('p_', true) . '.' . $ext;
      $target = $uploadDir . '/' . $filename;
      if (move_uploaded_file($file['tmp_name'], $target)) {
        $image_path = 'images/products/' . $filename;
      } else {
        $admin_msg = 'Erreur lors de l\'upload de l\'image.';
      }
    }
  }

  if (empty($admin_msg)) {
    if ($name === '' || $description === '') {
      $admin_msg = 'Nom et description requis.';
    } else {
      $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image_path) VALUES (?, ?, ?, ?)');
      $stmt->execute([$name, $description, $price, $image_path]);
      $admin_msg = 'Produit ajouté avec succès.';
    }
  }
}

// Fetch products to display
$stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BionicLife | Boutique</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<section class="featured-products">
  <h2>Nos Produits Bioniques</h2>

  <?php if (!empty($admin_msg)): ?>
    <div class="message <?= (strpos($admin_msg, 'succès') !== false) ? 'success' : 'error' ?>" style="max-width:820px; margin:10px auto;"><?= htmlspecialchars($admin_msg) ?></div>
  <?php endif; ?>

  <?php if (!empty($_SESSION['admin_msg'])): ?>
    <div class="message <?= (strpos($_SESSION['admin_msg'], 'succès') !== false) ? 'success' : 'error' ?>" style="max-width:820px; margin:10px auto;"><?= htmlspecialchars($_SESSION['admin_msg']) ?></div>
    <?php unset($_SESSION['admin_msg']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <div class="auth-container" style="max-width:820px; margin:20px auto;">
      <h3 class="auth-header">Ajouter un produit</h3>
      <form method="POST" enctype="multipart/form-data" class="auth-form">
        <div class="form-group">
          <label>Nom du produit</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4" required style="padding:10px; border-radius:8px; border:1px solid #dcdcdc;"></textarea>
        </div>
        <div class="form-group">
          <label>Prix (TND)</label>
          <input type="number" step="0.01" name="price" required>
        </div>
        <div class="form-group">
          <label>Image</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn">Ajouter</button>
      </form>
    </div>
  <?php endif; ?>

  <div class="product-grid">
    <?php if (empty($products)): ?>
      <p style="text-align:center; width:100%;">Aucun produit pour le moment.</p>
    <?php endif; ?>

    <?php foreach ($products as $p): ?>
      <div class="product-card">
        <?php if (!empty($p['image_path']) && file_exists(__DIR__ . '/' . $p['image_path'])): ?>
          <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <?php else: ?>
          <img src="images/produit.jpg" alt="<?= htmlspecialchars($p['name']) ?>">
        <?php endif; ?>
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
        <p style="font-weight:700; color:#1a1a1a;">Prix: <?= number_format($p['price'], 2) ?> TND</p>
        <div class="see-more-container">
          <a href="payement.php?id=<?= (int)$p['id'] ?>" class="btn">Acheter</a>
          <a href="#" class="btn-secondary">À propos</a>
        </div>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <div style="display:flex; gap:8px; margin-top:10px;">
            <a href="edit_product.php?id=<?= (int)$p['id'] ?>" class="btn" style="background:#ffb703;">Modifier</a>
            <form method="POST" action="delete_product.php" onsubmit="return confirm('Supprimer ce produit ?');" style="display:inline-block;">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button type="submit" class="btn-secondary" style="background:#e63946; border:0; padding:10px 12px; color:#fff; border-radius:10px;">Supprimer</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

  </div>

</section>

<footer>
  <p>&copy; 2025 BionicLife. Tous droits réservés.</p>
</footer>

</body>
</html>