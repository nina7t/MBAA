<?php

// Import des classes PHPMailer nécessaires pour l'envoi d'emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Chargement de l'autoloader de Composer pour accéder aux dépendances
require './autoload.php';

// RÉCUPÉRATION DES DONNÉES DU FORMULAIRE

// Le bug ici était que les variables n'étaient pas définies
// PHP affichait "Undefined variable" car $_POST n'était pas lu
$nom = $_POST['nom'] ?? '';
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$ville = $_POST['ville'] ?? '';
$message_user = $_POST['message_user'] ?? '';


// CRÉATION DU CONTENU DE L'EMAIL (HTML)

$subject = "Nouvelle demande de contact";
$message = "

<html>
<head>
<style>
body{
    font-family: Arial, sans-serif;
    color: #333; 
}

.container{
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    border: 1px solid #ddd;
    padding: 20px;
    background-color: #f9f9f9;
}

.logo{
    text-align: center;
    margin-bottom: 20px;
}

h2{
    color: #41a32bff;
    text-align: center;
}

table{
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

td{
    padding: 10px;
    border: 1px solid #ddd;
}



td.label{
    font-weight: bold;
    width: 40px;
    background-color: #2B6CA3;
    color: #fff;
}
td.value{

width: 60%;

    
}


.footer{
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    color: #666;
}


</style>
</head>
<body>

<div class='container'>
// logo

<div class='logo'>
<img src='https://mbaa.ma/wp-content/uploads/2025/06/mbaa-logo.png' alt='mbaa-logo'>
</div>
<h2>Nouvelle demande de contact</h2>


// Détails de la demande

<table>
<tr>
<td class='label'>Nom</td>
<td class='value'>$nom</td>
</tr>
<tr>
<td class='label'>Email</td>
<td class='value'>$email</td>
</tr>
<tr>
<td class='label'>Telephone</td>
<td class='value'>$telephone</td>
</tr>
<tr>
<td class='label'>Ville</td>
<td class='value'>$ville</td>
</tr>
<tr>
<td class='label'>Message</td>
<td class='value'>$message_user</td>
</tr>
</table>


<div class='footer'>
<p>Ce message à été envoyé par le formulaire de contact de la page d'accueil de MBAA</p>
<p>Cordialement</p>
<p>MBAA</p>
</div>

</div>

</body>
</html>";


// CONFIGURATION ET ENVOI DE L'EMAIL

$mail = new PHPMailer(true);

try {
	// DEBUG : Active les messages détaillés pour le développement
	$mail->SMTPDebug = 2; 
    $mail->isSMTP(); // Utilise SMTP pour l'envoi
 
	// Configuration du serveur SMTP (Gmail)
	$mail->Host ='smtp.gmail.com';
	$mail->SMTPAuth = true;
	$mail->Username = 'nina.tonnaire@gmail.com';
	$mail->Password = 'urjk hoqc beet prkm'; //   Mot de passe d'application Gmail !
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Port = 587;

	// Configuration de l'expéditeur et destinataire
	$mail->setFrom('nina.tonnaire@gmail.com', 'mon adresse');
	$mail->addAddress('marcelin.levillier@gmail.com'); // Change ce destinataire !
	    
    // Format de l'email
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    

    // $mail->addEmbeddedImage('/vendor/assets/img/mbaa-logo.png', 'logo_cid', 'logo.png');
    
    $mail->Subject = $subject;
    $mail->Body    = $message;

    // Envoi de l'email
    $mail->send();
    
    // Affichage pour le debug (à remplacer par redirection en production)
    echo "Email envoyé avec succès!";
    echo "<pre>";
    print_r($mail);
    echo "</pre>";
    // header("Location: nous-contacter.html?success=1"); // À décommenter en production
    exit;
    
} catch (Exception $e) {
    // Gestion des erreurs avec affichage détaillé pour le debug
    echo "❌ Erreur d'envoi : " . $mail->ErrorInfo;
    echo "<pre>";
    print_r($e);
    echo "</pre>";
    error_log("Erreur PHPMailer : {$mail->ErrorInfo}");
    // header("Location: nous-contacter.html?error=1"); // À décommenter en production
    exit;
}

?>