<?php
/**
 * Template Name: Générateur QR Code
 */

get_header();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Générateur QR Code – WordPress</title>

<!-- J'utilise une bibliothèque externe qui fait tout le boulot pour générer le QR code,
     pas besoin de coder ça à la main, elle est dispo gratuitement sur internet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>

  /* J'importe deux polices depuis Google Fonts :
     - Space Mono : pour les trucs techniques (URL, numéros...)
     - DM Sans : pour le texte normal, plus lisible */
  @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap');

  /* Mes couleurs globales — je les mets ici une seule fois
     et je les réutilise partout dans le CSS avec var(--nomdelacouleur)
     Comme ça si je veux changer une couleur, je le fais qu'à un endroit */
  :root {
    --bg: #0d0f14;         /* fond principal très sombre */
    --surface: #161a24;    /* fond des grandes zones */
    --card: #1e2330;       /* fond des petites cartes */
    --accent: #00e5a0;     /* vert fluo pour les trucs importants */
    --accent2: #0091ff;    /* bleu pour varier */
    --text: #e8eaf0;       /* texte principal (quasi blanc) */
    --muted: #7a8099;      /* texte secondaire (gris) */
    --border: #2a2f42;     /* couleur des bordures */
  }

  /* Reset basique — j'enlève les marges/paddings par défaut
     que les navigateurs mettent tout seuls, sinon ça fout le bordel */
  * { box-sizing: border-box; margin: 0; padding: 0; }

  /* La page entière : fond sombre, tout centré verticalement */
  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 40px 20px 60px;
  }

  /* Le bloc titre en haut de la page */
  .header {
    text-align: center;
    margin-bottom: 40px;
  }

  /* La petite pastille verte "Outil WordPress" au-dessus du titre */
  .badge {
    display: inline-block;
    background: rgba(0, 229, 160, 0.12); /* vert transparent */
    color: var(--accent);
    font-family: 'Space Mono', monospace;
    font-size: 11px;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid rgba(0,229,160,0.25);
    margin-bottom: 16px;
  }

  /* Le titre principal, la taille s'adapte selon la largeur de l'écran */
  h1 {
    font-size: clamp(26px, 5vw, 40px); /* min 26px, max 40px */
    font-weight: 600;
    letter-spacing: -0.5px;
    line-height: 1.2;
  }

  /* Le mot "QR Codes" dans le titre a un dégradé vert → bleu */
  h1 span {
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent; /* astuce CSS pour colorier uniquement le texte */
  }

  /* Sous-titre grisé en dessous du titre */
  .subtitle {
    color: var(--muted);
    margin-top: 10px;
    font-size: 15px;
    font-weight: 300;
  }

  /* ============================
     LES 3 ÉTAPES EXPLICATIVES
     ============================ */

  /* La ligne qui contient les 3 blocs d'étapes côte à côte */
  .steps {
    display: flex;
    gap: 12px;
    margin-bottom: 32px;
    flex-wrap: wrap; /* si l'écran est petit, ça passe en dessous */
    justify-content: center;
    max-width: 720px;
    width: 100%;
  }

  /* Un bloc d'étape individuel */
  .step {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 18px;
    flex: 1;
    min-width: 180px; /* jamais plus petit que ça */
    display: flex;
    align-items: flex-start;
    gap: 12px;
  }

  /* Le rond avec le numéro (1, 2, 3) — dégradé vert/bleu, texte noir */
  .step-num {
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    color: #000;
    font-family: 'Space Mono', monospace;
    font-size: 12px;
    font-weight: 700;
    width: 26px;
    height: 26px;
    border-radius: 50%; /* cercle parfait */
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0; /* le rond ne rétrécit pas si le texte est long */
    margin-top: 2px;
  }

  /* Titre en gras de l'étape */
  .step-text strong {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 3px;
  }

  /* Description grisée de l'étape */
  .step-text span {
    font-size: 12px;
    color: var(--muted);
    line-height: 1.4;
  }

  /* ============================
     LA CARTE PRINCIPALE (formulaire)
     ============================ */

  .card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 32px;
    width: 100%;
    max-width: 720px;
  }

  /* Les petits labels au-dessus des champs ("URL de ta page", "Taille"...) */
  label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
  }

  /* Le champ URL + le bouton "Générer" sur la même ligne */
  .input-row {
    display: flex;
    gap: 10px;
  }

  /* Le champ de saisie de l'URL */
  input[type="text"] {
    flex: 1; /* prend tout l'espace disponible */
    background: var(--card);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    color: var(--text);
    font-family: 'Space Mono', monospace;
    font-size: 14px;
    padding: 14px 18px;
    outline: none;
    transition: border-color 0.2s; /* animation douce sur la bordure */
  }

  /* Quand je clique dans le champ, la bordure devient verte */
  input[type="text"]:focus {
    border-color: var(--accent);
  }

  /* Le texte gris d'exemple dans le champ quand il est vide */
  input[type="text"]::placeholder {
    color: var(--muted);
    opacity: 0.6;
  }

  /* Style de base pour tous les boutons */
  button {
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    padding: 14px 22px;
    font-size: 14px;
    transition: all 0.2s;
  }

  /* Le bouton "Générer" — dégradé vert/bleu, texte noir */
  .btn-generate {
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    color: #000;
    white-space: nowrap; /* le texte du bouton ne se coupe jamais en 2 lignes */
  }

  /* Petit effet au survol : légèrement transparent + monte un peu */
  .btn-generate:hover {
    opacity: 0.88;
    transform: translateY(-1px);
  }

  /* ============================
     LES OPTIONS (taille, couleurs)
     ============================ */

  /* La ligne qui contient les options en dessous du champ URL */
  .options-row {
    display: flex;
    gap: 20px;
    margin-top: 22px;
    flex-wrap: wrap;
    align-items: flex-end;
  }

  /* Un groupe option = son label + son champ */
  .option-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .option-group label {
    margin-bottom: 0;
  }

  /* Le menu déroulant pour choisir la taille + les sélecteurs de couleur */
  select, input[type="color"] {
    background: var(--card);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    padding: 10px 14px;
    outline: none;
    cursor: pointer;
  }

  /* Le carré de couleur, un peu plus grand que la normale */
  input[type="color"] {
    width: 56px;
    height: 42px;
    padding: 4px 6px;
  }

  /* ============================
     LA ZONE DE RÉSULTAT (QR code généré)
     ============================ */

  /* Par défaut cette zone est CACHÉE — elle s'affiche seulement
     quand on ajoute la classe "visible" via JavaScript */
  .result-area {
    margin-top: 30px;
    display: none;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    padding-top: 28px;
    border-top: 1px solid var(--border); /* ligne séparatrice */
  }

  /* Quand JavaScript ajoute cette classe, la zone devient visible */
  .result-area.visible {
    display: flex;
  }

  /* Le petit texte vert "✓ QR Code généré" */
  .result-label {
    font-family: 'Space Mono', monospace;
    font-size: 12px;
    color: var(--accent);
    letter-spacing: 1.5px;
    text-transform: uppercase;
  }

  /* Le fond blanc autour du QR code (obligatoire pour que les scanners lisent bien) */
  #qr-container {
    background: white;
    padding: 18px;
    border-radius: 16px;
    display: inline-block;
    box-shadow: 0 0 40px rgba(0,229,160,0.15); /* halo vert autour */
  }

  /* L'URL affichée en petit sous le QR code */
  .url-display {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px 16px;
    font-family: 'Space Mono', monospace;
    font-size: 12px;
    color: var(--muted);
    word-break: break-all; /* si l'URL est longue elle coupe à la ligne */
    text-align: center;
    max-width: 340px;
  }

  /* Le bouton "Télécharger" — style plus discret que le bouton Générer */
  .btn-download {
    background: var(--card);
    color: var(--text);
    border: 1.5px solid var(--border);
    padding: 12px 26px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* Au survol, la bordure et le texte passent en vert */
  .btn-download:hover {
    border-color: var(--accent);
    color: var(--accent);
    transform: translateY(-1px);
  }

  /* Le bloc conseil bleu en bas de page */
  .tip-box {
    margin-top: 28px;
    background: rgba(0, 145, 255, 0.07);
    border: 1px solid rgba(0, 145, 255, 0.2);
    border-radius: 12px;
    padding: 16px 20px;
    width: 100%;
    max-width: 720px;
    font-size: 13px;
    color: #7db8f7;
    line-height: 1.6;
  }

  .tip-box strong {
    color: var(--accent2);
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  /* Sur mobile (écran < 520px) :
     le champ URL et le bouton passent l'un en dessous de l'autre
     et je réduis un peu les paddings */
  @media (max-width: 520px) {
    .input-row { flex-direction: column; }
    .card { padding: 22px 18px; }
  }

</style>
</head>
<body>

<!-- ======= EN-TÊTE ======= -->
<div class="header">
  <div class="badge">🔗 Outil WordPress</div>
  <h1>Générateur de <span>QR Codes</span></h1>
  <p class="subtitle">Transforme n'importe quelle URL WordPress en QR code téléchargeable</p>
</div>

<!-- ======= LES 3 ÉTAPES ======= -->
<!-- J'explique visuellement comment utiliser l'outil avant le formulaire -->
<div class="steps">
  <div class="step">
    <div class="step-num">1</div>
    <div class="step-text">
      <strong>Copie l'URL</strong>
      <span>Va sur ta page WP → clique sur « Voir la page » → copie l'URL dans la barre du navigateur</span>
    </div>
  </div>
  <div class="step">
    <div class="step-num">2</div>
    <div class="step-text">
      <strong>Colle & génère</strong>
      <span>Colle l'URL dans le champ ci-dessous puis clique sur « Générer »</span>
    </div>
  </div>
  <div class="step">
    <div class="step-num">3</div>
    <div class="step-text">
      <strong>Télécharge</strong>
      <span>Clique sur « Télécharger » pour sauvegarder le QR code en image PNG</span>
    </div>
  </div>
</div>

<!-- ======= FORMULAIRE PRINCIPAL ======= -->
<div class="card">

  <label>URL de ta page WordPress</label>

  <!-- Le champ texte + le bouton sont sur la même ligne grâce à .input-row -->
  <div class="input-row">
    <input type="text" id="url-input" placeholder="https://monsite.com/ma-page/" />
    <!-- onclick appelle ma fonction generateQR() définie plus bas en JS -->
    <button class="btn-generate" onclick="generateQR()">✦ Générer</button>
  </div>

  <!-- Les options de personnalisation du QR code -->
  <div class="options-row">
    <div class="option-group">
      <label>Taille</label>
      <!-- Je propose 4 tailles, 200px est sélectionné par défaut -->
      <select id="size-select">
        <option value="128">Petite – 128px</option>
        <option value="200" selected>Moyenne – 200px</option>
        <option value="300">Grande – 300px</option>
        <option value="400">Très grande – 400px</option>
      </select>
    </div>
    <div class="option-group">
      <label>Couleur QR</label>
      <!-- Sélecteur de couleur natif du navigateur pour les modules du QR -->
      <input type="color" id="color-dark" value="#000000" title="Couleur des modules">
    </div>
    <div class="option-group">
      <label>Fond</label>
      <!-- Sélecteur pour la couleur de fond (blanc par défaut) -->
      <input type="color" id="color-light" value="#ffffff" title="Couleur de fond">
    </div>
  </div>

  <!-- ======= RÉSULTAT =======
       Cette zone est invisible au départ (display:none en CSS)
       JavaScript lui ajoute la classe "visible" après génération -->
  <div class="result-area" id="result-area">
    <span class="result-label">✓ QR Code généré</span>
    <!-- C'est ici que la bibliothèque QRCode.js va injecter le QR en image -->
    <div id="qr-container"></div>
    <!-- Je réaffiche l'URL sous le QR pour confirmer ce qui est encodé -->
    <div class="url-display" id="url-display"></div>
    <!-- Ce bouton appelle downloadQR() qui récupère l'image et la télécharge -->
    <button class="btn-download" onclick="downloadQR()">
      ⬇ Télécharger le QR Code (PNG)
    </button>
  </div>

</div>

<!-- ======= CONSEIL IMPORTANT EN BAS =======
     Je préviens l'utilisateur que si l'URL change dans WP,
     il faut régénérer le QR code -->
<div class="tip-box">
  <strong>💡 Conseil d'utilisation</strong>
  Ce QR code pointe directement vers l'URL que tu as saisie. Si tu modifies l'URL de ta page dans WordPress, le QR code ne fonctionnera plus — <em>pense à en régénérer un nouveau à chaque changement d'URL.</em>
</div>


<script>

  // Je stocke le QR code ici pour pouvoir y accéder depuis les deux fonctions
  let currentQR = null;

  // ======= FONCTION PRINCIPALE =======
  // Elle se déclenche quand on clique sur "Générer"
  function generateQR() {

    // Je récupère ce que l'utilisateur a tapé dans le champ URL
    // .trim() enlève les espaces accidentels au début et à la fin
    const url = document.getElementById('url-input').value.trim();

    // Vérification 1 : le champ est vide ?
    if (!url) {
      alert('⚠️ Colle d\'abord une URL dans le champ !');
      return; // j'arrête la fonction là
    }

    // Vérification 2 : ça ressemble bien à une URL ?
    if (!url.startsWith('http')) {
      alert('⚠️ L\'URL doit commencer par http:// ou https://');
      return;
    }

    // Je récupère les valeurs des options choisies par l'utilisateur
    const size       = parseInt(document.getElementById('size-select').value);
    const darkColor  = document.getElementById('color-dark').value;
    const lightColor = document.getElementById('color-light').value;

    // Je vide le conteneur au cas où un QR code avait déjà été généré avant
    const container = document.getElementById('qr-container');
    container.innerHTML = '';

    // Je crée le QR code avec la bibliothèque QRCode.js
    // Elle génère automatiquement un <canvas> avec le QR code dedans
    currentQR = new QRCode(container, {
      text: url,                          // l'URL à encoder
      width: size,                        // largeur en pixels
      height: size,                       // hauteur en pixels
      colorDark: darkColor,               // couleur des carrés
      colorLight: lightColor,             // couleur du fond
      correctLevel: QRCode.CorrectLevel.H // niveau de correction max (plus robuste)
    });

    // J'affiche l'URL sous le QR code pour confirmation
    document.getElementById('url-display').textContent = url;

    // J'affiche la zone résultat en ajoutant la classe CSS "visible"
    document.getElementById('result-area').classList.add('visible');
  }


  // ======= FONCTION DE TÉLÉCHARGEMENT =======
  // Elle récupère l'image du QR code et la télécharge sur l'ordi
  function downloadQR() {

    // La bibliothèque a créé un élément <canvas> dans le conteneur,
    // je le récupère ici
    const canvas = document.querySelector('#qr-container canvas');

    // Sécurité : si pas de canvas (QR pas encore généré), j'arrête
    if (!canvas) return;

    // Je crée un lien fictif, je lui mets l'image en source,
    // et je simule un clic dessus — ça déclenche le téléchargement
    const link = document.createElement('a');
    link.download = 'qrcode-wordpress.png'; // nom du fichier téléchargé
    link.href = canvas.toDataURL('image/png'); // l'image convertie en données
    link.click(); // "clic" automatique pour lancer le téléchargement
  }


  // ======= BONUS : GÉNÉRER AVEC LA TOUCHE ENTRÉE =======
  // Pratique pour ne pas obligatoirement cliquer sur le bouton
  document.getElementById('url-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') generateQR();
  });

</script>

</body>
</html>

<?php get_footer(); ?>
