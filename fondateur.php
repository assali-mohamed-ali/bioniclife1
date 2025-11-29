<?php include __DIR__ . '/config/header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notre Équipe - BionicLife</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .team-section {
      max-width: 1300px;
      margin: 80px auto;
      padding: 20px;
      text-align: center;
    }

    .team-section h2 {
      font-size: 2.6rem;
      color: #00b4d8;
      margin-bottom: 4rem;
    }

    /* Conteneur principal */
    .team-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3rem;
      max-width: 1100px;
      margin: 0 auto;
    }

    /* Première ligne : 3 cartes */
    .team-row-1 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2.5rem;
      width: 100%;
    }

    /* Deuxième ligne : 2 cartes centrées */
    .team-row-2 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 2.5rem;
      width: 66%; /* environ 2/3 de la largeur = parfaitement centré */
      max-width: 750px;
    }

    .team-card {
      background: white;
      border-radius: 18px;
      padding: 2rem 1.5rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: all 0.4s ease;
    }

    .team-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 25px 50px rgba(0,180,216,0.25);
    }

    .team-photo {
      width: 160px;
      height: 160px;
      border-radius: 50%;
      object-fit: cover;
      border: 6px solid #00b4d8;
      margin-bottom: 1.5rem;
    }

    .team-name {
      font-size: 1.45rem;
      font-weight: 700;
      color: #1a1a1a;
      margin: 0.6rem 0;
    }

    .team-role {
      font-size: 1.15rem;
      color: #e63946;
      font-weight: 600;
      margin-bottom: 1.2rem;
    }

    .team-contact {
      font-size: 0.98rem;
      color: #555;
      line-height: 1.8;
    }

    .team-contact a {
      color: #00b4d8;
      text-decoration: none;
    }

    .team-contact a:hover { text-decoration: underline; }

    /* Responsive */
    @media (max-width: 992px) {
      .team-row-1 { grid-template-columns: 1fr 1fr; }
      .team-row-2 { width: 100%; grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 640px) {
      .team-row-1, .team-row-2 {
        grid-template-columns: 1fr;
        width: 100%;
        max-width: 380px;
      }
    }
  </style>
</head>
<body>

<section class="team-section">
  <h2>Notre Équipe</h2>

  <div class="team-wrapper">

    <!-- Ligne 1 : 3 personnes -->
    <div class="team-row-1">
      <div class="team-card">
        <img src="images/fondateur.jpg" alt="Mohamed" class="team-photo">
        <h3 class="team-name">Ayoub Bourguiba</h3>
        <div class="team-contact">
          <a href="tel:+21621369203">+216 21 369 203</a><br>
          <a href="bouruibaa02@gmail.com">bouruibaa02@gmail.com</a>
        </div>
      </div>

      <div class="team-card">
        <img src="images/ahmed.jpg" alt="Ahmed" class="team-photo">
        <h3 class="team-name">Mohamed Ben Nayma</h3>
        <div class="team-contact">
          <a href="tel:+21625577542">+216 25 577 542</a><br>
          <a href="Medbennaima2021@gmail.com">Medbennaima2021@gmail.com</a>
        </div>
      </div>

      <div class="team-card">
        <img src="images/sarah.jpg" alt="Sarah" class="team-photo">
        <h3 class="team-name">Ela Bayoudh </h3>
        <div class="team-contact">
          <a href="tel:+21629203089">+216 29 203 089</a><br>
          <a href="Ela.Bayoudh@esprim.tn">Ela.Bayoudh@esprim.tn</a>
        </div>
      </div>
    </div>

    <!-- Ligne 2 : 2 personnes bien centrées -->
    <div class="team-row-2">
      <div class="team-card">
        <img src="images/karim.jpg" alt="Karim" class="team-photo">
        <h3 class="team-name">Mohamed Ali Assali</h3>
        <div class="team-contact">
          <a href="tel:+21623490115">+216 23 490 115</a><br>
          <a href="assali.mohamedali@esprim.tn">assali.mohamedali@esprim.tn</a>
        </div>
      </div>

      <div class="team-card">
        <img src="images/aa.jpg" alt="Amina" class="team-photo">
        <h3 class="team-name">Oussama Toumi</h3>
        <div class="team-contact">
          <a href="tel:+21696327415">+216 96 327 415</a><br>
          <a href="toumioussama737@gmail.com">toumioussama737@gmail.com</a>
        </div>
      </div>
    </div>

  </div>
</section>

</body>
</html>