<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "toumioussama737@gmail.com"; // 👉 C'est ici que tu mets ton email
    $subject = "Nouveau rendez-vous depuis BionicLife";

    $message = "🗓️ Date : " . $_POST["date"] . "\n";
    $message .= "⏰ Heure : " . $_POST["time"] . "\n";
    $message .= "👤 Nom : " . $_POST["lastname"] . "\n";
    $message .= "👤 Prénom : " . $_POST["firstname"] . "\n";
    $message .= "📧 Email : " . $_POST["email"] . "\n";
    $message .= "📞 Téléphone : " . $_POST["phone"] . "\n";

    $headers = "From: no-reply@bioniclife.tn";

    // Fonction mail PHP
    if (mail($to, $subject, $message, $headers)) {
        header("Location: merci.php"); // page de confirmation (converted to .php)
    } else {
        echo "Erreur lors de l’envoi de l’email.";
    }
    exit;
}
?>
