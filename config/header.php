<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<header>
  <nav class="navbar">
    <div class="logo">BionicLife</div>
    <ul class="nav-links">
      <li><a href="index.php">Accueil</a></li>
      <li><a href="boutique.php">Nos produits</a></li>
      <li><a href="fondateur.php">Équipe</a></li>
<?php if (isset($_SESSION['user_id'])): ?>
      <li><a href="logout.php" class="btn-login">Déconnexion</a></li>
      <li style="margin-left:10px; color:#fff; font-weight:600;">Bonjour, <?= htmlspecialchars(
            isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Utilisateur'
        ) ?></li>
<?php else: ?>
      <li><a href="login.php" class="btn-login">Connexion</a></li>
<?php endif; ?>
    </ul>
  </nav>
</header>