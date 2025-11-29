<?php
// payement.php — renamed from rendezvous.php
include __DIR__ . '/config/header.php';
require_once __DIR__ . '/config/db.php';

// Ensure payments table exists (stores non-sensitive payment metadata only)
$pdo->exec("CREATE TABLE IF NOT EXISTS payments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  card_holder VARCHAR(255) NOT NULL,
  card_last4 VARCHAR(4) DEFAULT NULL,
  card_masked VARCHAR(64) DEFAULT NULL,
  expiry_month TINYINT DEFAULT NULL,
  expiry_year SMALLINT DEFAULT NULL,
  amount DECIMAL(10,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$form_error = '';
$form_success = '';
$product = null;

// get product id from GET (display) or POST (submit)
$requested_id = 0;
if (isset($_GET['id'])) $requested_id = intval($_GET['id']);
if (isset($_POST['product_id'])) $requested_id = intval($_POST['product_id']);

if ($requested_id > 0) {
  $pstm = $pdo->prepare('SELECT id, name, price FROM products WHERE id = ?');
  $pstm->execute([$requested_id]);
  $product = $pstm->fetch(PDO::FETCH_ASSOC);
  if (!$product) {
    $form_error = 'Produit introuvable.';
  }
}

// Process payment form. IMPORTANT: we DO NOT store full PAN or CVC.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_submit'])) {
  // Payment fields (will NOT store CVC)
  $card_holder = trim($_POST['card_holder'] ?? '');
  $card_number = preg_replace('/\D+/', '', ($_POST['card_number'] ?? ''));
  $expiry = trim($_POST['expiry'] ?? ''); // MM/YY or MM/YYYY
  $cvc = trim($_POST['cvc'] ?? '');
  // amount must come from the product price in the database (server authoritative)
  $product_id_post = intval($_POST['product_id'] ?? 0);
  if ($product_id_post <= 0) {
    $form_error = 'Produit non sélectionné.';
  } else {
    $pstm = $pdo->prepare('SELECT price, name FROM products WHERE id = ?');
    $pstm->execute([$product_id_post]);
    $prodRow = $pstm->fetch(PDO::FETCH_ASSOC);
    if (!$prodRow) {
      $form_error = 'Produit introuvable.';
    } else {
      $amount = floatval($prodRow['price']);
      // ensure product data is available for the UI if not already
      if (!$product) {
        $product = ['id' => $product_id_post, 'name' => $prodRow['name'], 'price' => $amount];
      }
    }
  }

  // basic validation
  if ($card_holder === '' || $card_number === '' || $expiry === '' || $cvc === '') {
    $form_error = 'Veuillez remplir tous les champs de paiement.';
  } elseif ($amount <= 0) {
    $form_error = 'Montant invalide.';
  } else {
    // check card number length: require exactly 12 digits (simplified requirement)
    if (strlen($card_number) !== 12) {
      $form_error = 'Le numéro de carte doit contenir exactement 12 chiffres.';
    }
  }

  // expiry validation
  if ($form_error === '') {
    // accept MM/YY or MM/YYYY
    if (preg_match('/^(0[1-9]|1[0-2])\/(\d{2}|\d{4})$/', $expiry, $m)) {
      $month = intval($m[1]);
      $year = intval($m[2]);
      if ($year < 100) $year += 2000;
      // check expiry not in past
      $nowY = intval(date('Y'));
      $nowM = intval(date('n'));
      if ($year < $nowY || ($year == $nowY && $month < $nowM)) {
        $form_error = 'Date d\'expiration invalide.';
      }
    } else {
      $form_error = 'Date d\'expiration invalide (format MM/YY).';
    }
  }

  if ($form_error === '') {
    // Prepare safe values — we DO NOT store CVC, we store masked card and last4 only
    $last4 = substr($card_number, -4);
    $masked = str_repeat('*', max(0, strlen($card_number)-4)) . $last4;

    $stmt = $pdo->prepare('INSERT INTO payments (card_holder, card_last4, card_masked, expiry_month, expiry_year, amount) VALUES (?, ?, ?, ?, ?, ?)');
    $ok = $stmt->execute([$card_holder, $last4, $masked, $month, $year, $amount]);
    if ($ok) {
      $insertedId = $pdo->lastInsertId();
      $form_success = 'Merci — paiement enregistré avec succès (ID: ' . htmlspecialchars($insertedId) . ').';
      if ($product) {
        $form_success .= ' Vous avez acheté "' . htmlspecialchars($product['name']) . '" pour ' . number_format($amount, 2) . ' TND.';
      }
      // keep on page so the admin/user can verify database
    } else {
      $form_error = 'Erreur lors de l\'enregistrement. Réessayez.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Payement – BionicLife</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- FORM -->
<section class="form-section">
    <form action="payement.php" method="POST" class="appointment-form" onsubmit="return validatePaymentForm();">
    <?php if (!empty($form_error)): ?>
      <div class="message error" style="max-width:820px; margin:8px auto;"><?= htmlspecialchars($form_error) ?></div>
    <?php endif; ?>
    <?php if (!empty($form_success)): ?>
      <div class="message success" style="max-width:820px; margin:8px auto;"><?= htmlspecialchars($form_success) ?></div>
        <div style="text-align:center; margin-top:10px;">
          <a href="boutique.php" class="btn-secondary">← Retour à la boutique</a>
        </div>
    <?php endif; ?>

    <h2 style="text-align:left; width:100%; margin-bottom:1rem;">Paiement sécurisé</h2>
    <div class="form-group">
      <label for="card_holder">Nom sur la carte</label>
      <input type="text" name="card_holder" id="card_holder" required>
    </div>
    <div class="form-group">
      <label for="card_number">Numéro de carte</label>
      <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" required>
    </div>
    <div class="form-group" style="display:flex; gap:10px;">
      <div style="flex:1;">
        <label for="expiry">Expiration (MM/YY)</label>
        <input type="text" name="expiry" id="expiry" placeholder="MM/YY" required>
      </div>
      <div style="width:110px;">
        <label for="cvc">CVC</label>
        <input type="text" name="cvc" id="cvc" placeholder="123" required>
      </div>
    </div>

    <?php if ($product): ?>
      <div class="form-group">
        <label>Produit</label>
        <div style="padding:10px 12px; background:#f8f8f8; border-radius:8px; border:1px solid #eee;"><?= htmlspecialchars($product['name']) ?></div>
      </div>
      <div class="form-group">
        <label>Montant (TND)</label>
        <div style="padding:10px 12px; background:#fff; border-radius:8px; border:1px solid #eee; font-weight:700;"><?= number_format($product['price'], 2) ?> TND</div>
      </div>
      <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
    <?php else: ?>
      <div class="message error" style="max-width:820px; margin:8px auto;">Aucun produit sélectionné pour le paiement.</div>
    <?php endif; ?>

    <input type="hidden" name="pay_submit" value="1">
    <button type="submit" class="btn" <?= $product ? '' : 'disabled' ?>>Valider</button>
    <p id="confirmation" class="confirmation-msg"></p>
    <div class="back-home-container">
        <a href="index.php" class="btn-secondary">← Retour à l’accueil</a>
      </div>
      
  </form>
</section>

<!-- FOOTER -->
<footer class="footer">
  <p>© 2025 BionicLife</p>
</footer>

<script>
  function validatePaymentForm() {
    const cardNumber = document.getElementById('card_number').value.replace(/\D/g, '');
    if (cardNumber.length !== 12) {
      alert('Le numéro de carte doit contenir exactement 12 chiffres.');
      return false;
    }
    const cvc = document.getElementById('cvc').value.trim();
    if (cvc.length < 3 || cvc.length > 4) {
      alert('CVC invalide.');
      return false;
    }
    const expiry = document.getElementById('expiry').value.trim();
    if (!/^(0[1-9]|1[0-2])\/(\d{2}|\d{4})$/.test(expiry)) {
      alert('Format d\'expiration invalide (MM/YY).');
      return false;
    }
    const parts = expiry.split('/');
    let m = parseInt(parts[0], 10);
    let y = parseInt(parts[1], 10);
    if (y < 100) y += 2000;
    const now = new Date();
    if (y < now.getFullYear() || (y == now.getFullYear() && m < (now.getMonth()+1))) {
      alert('La carte est expirée.');
      return false;
    }
    // product price is taken from the server; no client-editable amount to validate here
    return true;
  }
  // Luhn validation removed — we only require exactly 12 digits per spec
</script>

</body>
</html>
