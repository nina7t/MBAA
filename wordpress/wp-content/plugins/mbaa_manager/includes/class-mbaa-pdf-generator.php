<?php
/**
 * Classe de génération de PDF avec DOMPDF
 * MBAA PDF Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

use Dompdf\Dompdf;
use Dompdf\Options;

class MBAA_PDF_Generator {

    private $dompdf;

    public function __construct() {
        $this->init_dompdf();
    }

    /**
     * Initialiser DOMPDF avec les options
     */
    private function init_dompdf() {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false); // Désactiver PHP pour sécurité

        $this->dompdf = new Dompdf($options);
    }

    /**
     * Générer un PDF à partir d'un template HTML
     *
     * @param string $html Le contenu HTML
     * @param string $filename Le nom du fichier de sortie
     * @param string $paper Le format de papier (A4, A3, Letter, etc.)
     * @param string $orientation L'orientation (portrait ou landscape)
     * @return bool|string False si erreur, ou le chemin du fichier PDF généré
     */
    public function generate_pdf($html, $filename = 'document.pdf', $paper = 'A4', $orientation = 'portrait') {
        try {
            // Charger le HTML
            $this->dompdf->loadHtml($html);

            // Configurer le papier
            $this->dompdf->setPaper($paper, $orientation);

            // Rendre le PDF
            $this->dompdf->render();

            // Générer le nom de fichier unique
            $upload_dir = wp_upload_dir();
            $pdf_dir = $upload_dir['basedir'] . '/mbaa-pdfs/';

            // Créer le dossier s'il n'existe pas
            if (!file_exists($pdf_dir)) {
                wp_mkdir_p($pdf_dir);
            }

            $file_path = $pdf_dir . $filename;

            // Sauvegarder le PDF
            $output = $this->dompdf->output();
            file_put_contents($file_path, $output);

            return $file_path;

        } catch (Exception $e) {
            error_log('MBAA PDF Generator Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Générer un PDF pour une œuvre
     *
     * @param int $oeuvre_id L'ID de l'œuvre
     * @return bool|string False si erreur, ou le chemin du fichier PDF
     */
    public function generate_oeuvre_pdf($oeuvre_id) {
        $oeuvre = get_post($oeuvre_id);

        if (!$oeuvre || $oeuvre->post_type !== 'oeuvre') {
            return false;
        }

        // Récupérer les métadonnées de l'œuvre
        $description = get_post_meta($oeuvre_id, 'mbaa_description', true);
        $date_creation = get_post_meta($oeuvre_id, 'mbaa_date_creation', true);
        $technique = get_post_meta($oeuvre_id, 'mbaa_technique', true);
        $dimensions = get_post_meta($oeuvre_id, 'mbaa_dimensions', true);
        $artiste_id = get_post_meta($oeuvre_id, 'mbaa_artiste_id', true);

        // Récupérer l'artiste
        $artiste = $artiste_id ? get_post($artiste_id) : null;

        // Générer le HTML
        $html = $this->get_oeuvre_pdf_template($oeuvre, $description, $date_creation, $technique, $dimensions, $artiste);

        $filename = 'oeuvre-' . $oeuvre->post_name . '.pdf';

        return $this->generate_pdf($html, $filename);
    }

    /**
     * Générer un PDF pour un artiste
     *
     * @param int $artiste_id L'ID de l'artiste
     * @return bool|string False si erreur, ou le chemin du fichier PDF
     */
    public function generate_artiste_pdf($artiste_id) {
        $artiste_manager = new MBAA_Artiste();
        $artiste = $artiste_manager->get_artiste($artiste_id);

        if (!$artiste) {
            return false;
        }

        // Récupérer les données de l'artiste depuis la table personnalisée
        $biographie = $artiste->biographie;
        $date_naissance = $artiste->date_naissance;
        $nationalite = $artiste->nationalite;

        // Générer le HTML
        $html = $this->get_artiste_pdf_template($artiste, $biographie, $date_naissance, $nationalite);

        $filename = 'artiste-' . sanitize_title($artiste->nom) . '.pdf';

        return $this->generate_pdf($html, $filename);
    }

    /**
     * Template HTML pour une œuvre
     */
    private function get_oeuvre_pdf_template($oeuvre, $description, $date_creation, $technique, $dimensions, $artiste) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Fiche Œuvre - ' . esc_html($oeuvre->post_title) . '</title>
            <style>
                body { font-family: "DejaVu Sans", sans-serif; margin: 40px; }
                .header { text-align: center; border-bottom: 2px solid #2B6CA3; padding-bottom: 20px; margin-bottom: 30px; }
                .logo { max-width: 150px; }
                h1 { color: #2B6CA3; margin-top: 20px; }
                .info-section { margin: 20px 0; }
                .info-label { font-weight: bold; color: #2B6CA3; }
                .info-value { margin-left: 10px; }
                .description { margin-top: 30px; line-height: 1.6; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . get_template_directory_uri() . '/assets/images/logo.png" alt="MBAA Logo" class="logo">
                <h1>Fiche descriptive d\'œuvre</h1>
            </div>

            <div class="info-section">
                <h2>' . esc_html($oeuvre->post_title) . '</h2>
            </div>

            <div class="info-section">
                <div class="info-label">Artiste:</div>
                <div class="info-value">' . ($artiste ? esc_html($artiste->post_title) : 'Non spécifié') . '</div>
            </div>

            <div class="info-section">
                <div class="info-label">Date de création:</div>
                <div class="info-value">' . ($date_creation ? esc_html($date_creation) : 'Non spécifiée') . '</div>
            </div>

            <div class="info-section">
                <div class="info-label">Technique:</div>
                <div class="info-value">' . ($technique ? esc_html($technique) : 'Non spécifiée') . '</div>
            </div>

            <div class="info-section">
                <div class="info-label">Dimensions:</div>
                <div class="info-value">' . ($dimensions ? esc_html($dimensions) : 'Non spécifiées') . '</div>
            </div>

            <div class="description">
                <div class="info-label">Description:</div>
                <div style="margin-top: 10px;">' . ($description ? wp_kses_post($description) : 'Aucune description disponible.') . '</div>
            </div>

            <div class="footer">
                <p>Musée des Beaux-Arts et d\'Archéologie</p>
                <p>Généré le ' . date('d/m/Y à H:i') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Template HTML pour un artiste
     */
    private function get_artiste_pdf_template($artiste, $biographie, $date_naissance, $nationalite) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Fiche Artiste - ' . esc_html($artiste->nom) . '</title>
            <style>
                body { font-family: "DejaVu Sans", sans-serif; margin: 40px; }
                .header { text-align: center; border-bottom: 2px solid #2B6CA3; padding-bottom: 20px; margin-bottom: 30px; }
                .logo { max-width: 150px; }
                h1 { color: #2B6CA3; margin-top: 20px; }
                .info-section { margin: 20px 0; }
                .info-label { font-weight: bold; color: #2B6CA3; }
                .info-value { margin-left: 10px; }
                .biographie { margin-top: 30px; line-height: 1.6; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . get_template_directory_uri() . '/assets/images/logo.png" alt="MBAA Logo" class="logo">
                <h1>Fiche artiste</h1>
            </div>

            <div class="info-section">
                <h2>' . esc_html($artiste->nom) . '</h2>
            </div>

            <div class="info-section">
                <div class="info-label">Date de naissance:</div>
                <div class="info-value">' . ($date_naissance ? esc_html($date_naissance) : 'Non spécifiée') . '</div>
            </div>

            <div class="info-section">
                <div class="info-label">Nationalité:</div>
                <div class="info-value">' . ($nationalite ? esc_html($nationalite) : 'Non spécifiée') . '</div>
            </div>

            <div class="biographie">
                <div class="info-label">Biographie:</div>
                <div style="margin-top: 10px;">' . ($biographie ? wp_kses_post($biographie) : 'Aucune biographie disponible.') . '</div>
            </div>

            <div class="footer">
                <p>Musée des Beaux-Arts et d\'Archéologie</p>
                <p>Généré le ' . date('d/m/Y à H:i') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Télécharger un PDF généré
     *
     * @param string $file_path Le chemin du fichier PDF
     * @param string $filename Le nom du fichier pour le téléchargement
     */
    public function download_pdf($file_path, $filename) {
        if (file_exists($file_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        }
    }
}

// Initialiser l'autoloader de Composer
require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';
