<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Classe pour gérer le formulaire de contact
 */

class MBAA_Contact {
    
    public function __construct() {
        add_shortcode('mbaa_contact_form', array($this, 'render_contact_form'));
        add_action('wp_ajax_mbaa_send_contact', array($this, 'send_contact'));
        add_action('wp_ajax_nopriv_mbaa_send_contact', array($this, 'send_contact'));
    }
    
    /**
     * Afficher le formulaire de contact
     */
    public function render_contact_form() {
        ob_start();
        ?>
        <div class="mbaa-contact-form">
            <h2>Nous contacter</h2>
            
            <?php if (isset($_GET['contact_success'])): ?>
                <div class="alert alert-success">
                    Votre message a été envoyé avec succès !
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['contact_error'])): ?>
                <div class="alert alert-error">
                    Une erreur est survenue lors de l'envoi du message.
                </div>
            <?php endif; ?>
            
            <form id="mbaa-contact-form" method="POST">
                <?php wp_nonce_field('mbaa_contact_nonce', 'contact_nonce'); ?>
                
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone">
                </div>
                
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville">
                </div>
                
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Envoyer le message</button>
            </form>
        </div>
        
        <style>
        .mbaa-contact-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn-submit {
            background-color: #2B6CA3;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-submit:hover {
            background-color: #1e4d7a;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        </style>
        
        <script>
        document.getElementById('mbaa-contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'mbaa_send_contact');
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = window.location.pathname + '?contact_success=1';
                } else {
                    window.location.href = window.location.pathname + '?contact_error=1';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = window.location.pathname + '?contact_error=1';
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Traiter l'envoi du formulaire
     */
    public function send_contact() {
        // Vérifier le nonce
        if (!wp_verify_nonce($_POST['contact_nonce'], 'mbaa_contact_nonce')) {
            wp_die('Sécurité invalide');
        }
        
        // Vérifier les champs requis
        if (empty($_POST['nom']) || empty($_POST['email']) || empty($_POST['message'])) {
            wp_send_json_error('Champs requis manquants');
        }
        
        // Inclure PHPMailer
        require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';
        
        $nom = sanitize_text_field($_POST['nom']);
        $email = sanitize_email($_POST['email']);
        $telephone = sanitize_text_field($_POST['telephone']);
        $ville = sanitize_text_field($_POST['ville']);
        $message_user = sanitize_textarea_field($_POST['message']);
        
        $subject = "Nouvelle demande de contact";
        $html_message = "
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
            color: #2B6CA3;
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
            width: 40%;
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
        <div class='logo'>
        <img src='https://mbaa.ma/wp-content/uploads/2025/06/mbaa-logo.png' alt='mbaa-logo'>
        </div>
        <h2>Nouvelle demande de contact</h2>
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
        <td class='label'>Téléphone</td>
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
        <p>Ce message a été envoyé par le formulaire de contact de la page d'accueil de MBAA</p>
        <p>Cordialement</p>
        <p>MBAA</p>
        </div>
        </div>
        </body>
        </html>";
        
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nina.tonnaire@gmail.com';
            $mail->Password = 'urjk hoqc beet prkm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom('nina.tonnaire@gmail.com', 'MBAA Contact');
            $mail->addAddress('marcelin.levillier@gmail.com');
            $mail->addReplyTo($email, $nom);
            
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $html_message;
            
            $mail->send();
            wp_send_json_success('Message envoyé');
            
        } catch (Exception $e) {
            error_log("Erreur PHPMailer : {$mail->ErrorInfo}");
            wp_send_json_error('Erreur d\'envoi');
        }
    }
}
