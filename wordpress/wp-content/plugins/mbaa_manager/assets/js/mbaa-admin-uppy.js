/**
 * MBAA Plugin - Admin JavaScript avec Uppy
 * Interface moderne pour musée d'art avec upload haute résolution
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    /* ========================================
           CONFIGURATION UPPY POUR MUSÉE
        ======================================== */

    // Vérifier si Uppy est chargé
    if (typeof Uppy === "undefined") {
      console.error(
        "Uppy n'est pas chargé. Veuillez inclure les scripts Uppy."
      );
      return;
    }

    // Configuration globale pour le musée
    const MUSEUM_CONFIG = {
      image: {
        maxFileSize: 50 * 1024 * 1024, // 50 MB pour haute résolution
        maxNumberOfFiles: 10,
        allowedFileTypes: [".jpg", ".jpeg", ".png", ".tif", ".tiff", ".webp"],
        minImageDimensions: [1920, 1080], // Minimum HD
      },
      audio: {
        maxFileSize: 100 * 1024 * 1024, // 100 MB pour audio haute qualité
        maxNumberOfFiles: 5,
        allowedFileTypes: [".mp3", ".wav", ".m4a", ".ogg", ".flac"],
      },
    };

    /* ========================================
           UPPY INSTANCE - IMAGES HAUTE-RES
        ======================================== */

    let imageUppy = null;

    function initImageUppy($container) {
      const $input = $container.find('input[type="url"]#image_url');
      if (!$input.length) return;

      // Créer un conteneur pour Uppy Dashboard
      const uppyId = "uppy-dashboard-image-" + Date.now();
      const $uppyContainer = $(
        '<div id="' +
          uppyId +
          '" class="mbaa-uppy-container" style="margin-top: 16px;"></div>'
      );
      $input.after($uppyContainer);

      imageUppy = new Uppy.Core({
        id: uppyId,
        autoProceed: false,
        allowMultipleUploadBatches: true,
        restrictions: {
          maxFileSize: MUSEUM_CONFIG.image.maxFileSize,
          maxNumberOfFiles: MUSEUM_CONFIG.image.maxNumberOfFiles,
          allowedFileTypes: MUSEUM_CONFIG.image.allowedFileTypes,
        },
        locale: {
          strings: {
            dropHereOr: "Déposez les images ici ou %{browse}",
            browse: "parcourez vos fichiers",
            uploadComplete: "Téléversement terminé",
            uploadFailed: "Échec du téléversement",
            xFilesSelected: {
              0: "%{smart_count} fichier sélectionné",
              1: "%{smart_count} fichiers sélectionnés",
            },
          },
        },
      });

      // Plugin Dashboard avec prévisualisation
      imageUppy.use(Uppy.Dashboard, {
        target: "#" + uppyId,
        inline: true,
        height: 400,
        width: "100%",
        showProgressDetails: true,
        hideUploadButton: false,
        hideRetryButton: false,
        hidePauseResumeButton: false,
        hideCancelButton: false,
        showRemoveButtonAfterComplete: true,
        proudlyDisplayPoweredByUppy: false,
        locale: {
          strings: {
            dropPasteImportBoth:
              "Déposez vos images ici, collez, %{browseFiles} ou importez depuis",
            dropPasteBoth: "Déposez vos images ici, collez ou %{browseFiles}",
            dropPasteImportFiles:
              "Déposez vos fichiers ici, collez, %{browseFiles} ou importez depuis",
            browseFiles: "parcourez",
          },
        },
      });

      // Plugin Image Editor pour recadrage/rotation
      imageUppy.use(Uppy.ImageEditor, {
        target: Uppy.Dashboard,
        quality: 0.95, // Haute qualité pour musée
        cropperOptions: {
          viewMode: 1,
          background: false,
          autoCropArea: 1,
          responsive: true,
        },
        actions: {
          revert: true,
          rotate: true,
          granularRotate: true,
          flip: true,
          zoomIn: true,
          zoomOut: true,
          cropSquare: true,
          cropWidescreen: true,
          cropWidescreenVertical: true,
        },
      });

      // Plugin Webcam (optionnel, pour documentation sur place)
      imageUppy.use(Uppy.Webcam, {
        target: Uppy.Dashboard,
        modes: ["picture"],
        mirror: false,
        facingMode: "environment",
        locale: {
          strings: {
            pluginNameCamera: "Appareil photo",
            smile: "Souriez !",
            takePicture: "Prendre une photo",
            startRecording: "Commencer l'enregistrement",
            stopRecording: "Arrêter l'enregistrement",
            allowAccessTitle: "Veuillez autoriser l'accès à votre caméra",
            allowAccessDescription:
              "Pour prendre des photos, veuillez autoriser l'accès à votre caméra dans votre navigateur.",
          },
        },
      });

      // Plugin URL pour import depuis web
      imageUppy.use(Uppy.Url, {
        target: Uppy.Dashboard,
        companionUrl: mbaaAjax.companionUrl || null,
        locale: {
          strings: {
            pluginNameUrl: "URL",
            enterUrlToImport: "Entrez l'URL pour importer un fichier",
            failedToFetch: "Échec de récupération de cette URL",
          },
        },
      });

      // Validation des dimensions d'image
      imageUppy.use(Uppy.Compressor, {
        quality: 0.95,
        limit: 10,
      });

      // XHR Upload vers WordPress
      imageUppy.use(Uppy.XHRUpload, {
        endpoint: mbaaAjax.ajaxurl + "?action=mbaa_uppy_upload",
        formData: true,
        fieldName: "file",
        headers: {
          "X-WP-Nonce": mbaaAjax.nonce,
        },
        getResponseData(responseText) {
          console.log("MBAA Uppy Response brute:", responseText);
          const response = JSON.parse(responseText);
          console.log("MBAA Uppy Response parsée:", response);
          
          if (response.success) {
            console.log("MBAA Uppy: Upload réussi, données:", response.data);
            return {
              url: response.data.url,
              id: response.data.id,
            };
          }
          console.error("MBAA Uppy: Erreur serveur:", response.data);
          throw new Error(response.data || "Upload failed");
        },
        bundle: false,
      });

      // Événements Uppy
      imageUppy.on("file-added", (file) => {
        console.log("Image ajoutée:", file.name);

        // Validation dimensions minimum
        if (file.type.startsWith("image/")) {
          const img = new Image();
          img.onload = function () {
            if (
              this.width < MUSEUM_CONFIG.image.minImageDimensions[0] ||
              this.height < MUSEUM_CONFIG.image.minImageDimensions[1]
            ) {
              imageUppy.info(
                `L'image ${file.name} est trop petite. Minimum requis: ${MUSEUM_CONFIG.image.minImageDimensions[0]}x${MUSEUM_CONFIG.image.minImageDimensions[1]}px`,
                "error",
                5000
              );
              imageUppy.removeFile(file.id);
            }
          };
          img.src = URL.createObjectURL(file.data);
        }
      });

      imageUppy.on("upload-success", (file, response) => {
        console.log("Upload réussi:", file.name, response);

        // Mettre à jour le champ input avec l'URL
        if (response.body && response.body.url) {
          $input.val(response.body.url).trigger("change");

          // Toast de succès
          if (typeof mbaaToast !== "undefined") {
            mbaaToast.success(
              `Image "${file.name}" téléversée avec succès ! (${formatFileSize(
                file.size
              )})`
            );
          }

          // Afficher l'aperçu
          showImagePreview($input, response.body.url);
        }
      });

      imageUppy.on("complete", (result) => {
        console.log("Téléversement terminé:", result);

        if (result.successful.length > 0) {
          const totalSize = result.successful.reduce(
            (acc, file) => acc + file.size,
            0
          );
          if (typeof mbaaToast !== "undefined") {
            mbaaToast.success(
              `${
                result.successful.length
              } image(s) téléversée(s) (${formatFileSize(totalSize)})`
            );
          }
        }

        if (result.failed.length > 0) {
          if (typeof mbaaToast !== "undefined") {
            mbaaToast.error(
              `${result.failed.length} image(s) ont échoué. Veuillez réessayer.`
            );
          }
        }
      });

      imageUppy.on("error", (error) => {
        console.error("Erreur Uppy:", error);
        if (typeof mbaaToast !== "undefined") {
          mbaaToast.error("Erreur: " + error.message);
        }
      });

      imageUppy.on("restriction-failed", (file, error) => {
        console.warn("Restriction échouée:", file, error);
        if (typeof mbaaToast !== "undefined") {
          mbaaToast.error(`Fichier rejeté: ${error.message}`);
        }
      });
    }

    /* ========================================
           UPPY INSTANCE - AUDIO GUIDES
        ======================================== */

    let audioUppy = null;

    function initAudioUppy($container) {
      const $input = $container.find(
        'input[type="url"]#audio_url, input[type="url"]#audio_biographie'
      );
      if (!$input.length) return;

      // Créer un conteneur pour Uppy Dashboard
      const uppyId = "uppy-dashboard-audio-" + Date.now();
      const $uppyContainer = $(
        '<div id="' +
          uppyId +
          '" class="mbaa-uppy-container" style="margin-top: 16px;"></div>'
      );
      $input.after($uppyContainer);

      audioUppy = new Uppy.Core({
        id: uppyId,
        autoProceed: false,
        allowMultipleUploadBatches: true,
        restrictions: {
          maxFileSize: MUSEUM_CONFIG.audio.maxFileSize,
          maxNumberOfFiles: MUSEUM_CONFIG.audio.maxNumberOfFiles,
          allowedFileTypes: MUSEUM_CONFIG.audio.allowedFileTypes,
        },
        locale: {
          strings: {
            dropHereOr: "Déposez les fichiers audio ici ou %{browse}",
            browse: "parcourez vos fichiers",
            uploadComplete: "Téléversement terminé",
            uploadFailed: "Échec du téléversement",
          },
        },
      });

      // Plugin Dashboard
      audioUppy.use(Uppy.Dashboard, {
        target: "#" + uppyId,
        inline: true,
        height: 350,
        width: "100%",
        showProgressDetails: true,
        hideUploadButton: false,
        proudlyDisplayPoweredByUppy: false,
        note: "Fichiers audio acceptés: MP3, WAV, M4A, OGG, FLAC (max 100 MB)",
        locale: {
          strings: {
            dropPasteImportBoth:
              "Déposez vos fichiers audio ici, %{browseFiles} ou importez depuis",
            browseFiles: "parcourez",
          },
        },
      });

      // Plugin Audio pour prévisualisation
      audioUppy.use(Uppy.Audio, {
        target: Uppy.Dashboard,
        showRecordingLength: true,
        locale: {
          strings: {
            pluginNameAudio: "Enregistrement audio",
            recording: "Enregistrement en cours",
            recordingLength: "Durée: %{recording_length}",
            startRecording: "Commencer l'enregistrement",
            stopRecording: "Arrêter l'enregistrement",
            allowAccessTitle: "Veuillez autoriser l'accès à votre microphone",
          },
        },
      });

      // XHR Upload
      audioUppy.use(Uppy.XHRUpload, {
        endpoint: mbaaAjax.ajaxurl + "?action=mbaa_uppy_upload",
        formData: true,
        fieldName: "file",
        headers: {
          "X-WP-Nonce": mbaaAjax.nonce,
        },
        getResponseData(responseText) {
          const response = JSON.parse(responseText);
          if (response.success) {
            return {
              url: response.data.url,
              id: response.data.id,
            };
          }
          throw new Error(response.data || "Upload failed");
        },
      });

      // Événements
      audioUppy.on("file-added", (file) => {
        console.log("Audio ajouté:", file.name);
      });

      audioUppy.on("upload-success", (file, response) => {
        console.log("Upload audio réussi:", file.name);

        if (response.body && response.body.url) {
          $input.val(response.body.url).trigger("change");

          // Toast de succès
          if (typeof mbaaToast !== "undefined") {
            mbaaToast.success(
              `Audio "${file.name}" téléversé avec succès ! (${formatFileSize(
                file.size
              )})`
            );
          }

          // Afficher le lecteur audio
          showAudioPlayer($input, response.body.url);
        }
      });

      audioUppy.on("complete", (result) => {
        console.log("Téléversement audio terminé:", result);

        if (result.successful.length > 0) {
          if (typeof mbaaToast !== "undefined") {
            mbaaToast.success(
              `${result.successful.length} fichier(s) audio téléversé(s)`
            );
          }
        }
      });
    }

    /* ========================================
           FONCTIONS UTILITAIRES
        ======================================== */

    function formatFileSize(bytes) {
      if (bytes === 0) return "0 Bytes";
      const k = 1024;
      const sizes = ["Bytes", "KB", "MB", "GB"];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
    }

    function showImagePreview($input, url) {
      // Supprimer l'ancien aperçu
      $input.siblings(".mbaa-image-preview").remove();

      // Créer le nouvel aperçu
      const $preview = $('<div class="mbaa-image-preview"></div>');
      const $img = $(
        '<img src="' +
          url +
          '" alt="Aperçu" style="max-width: 300px; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 16px;">'
      );
      const $remove = $(
        '<button type="button" class="button" style="display: block; margin-top: 8px;">Supprimer l\'image</button>'
      );

      $remove.on("click", function () {
        $input.val("").trigger("change");
        $preview.remove();
        if (imageUppy) {
          imageUppy.reset();
        }
      });

      $preview.append($img).append($remove);
      $input.after($preview);
    }

    function showAudioPlayer($input, url) {
      // Supprimer l'ancien lecteur
      $input.siblings(".mbaa-audio-player").remove();

      // Créer le nouveau lecteur
      const $player = $('<div class="mbaa-audio-player"></div>');
      const $audio = $(
        '<audio controls style="width: 100%; margin-top: 16px;"><source src="' +
          url +
          '" type="audio/mpeg">Votre navigateur ne supporte pas l\'élément audio.</audio>'
      );
      const $remove = $(
        '<button type="button" class="button" style="display: block; margin-top: 8px;">Supprimer l\'audio</button>'
      );

      $remove.on("click", function () {
        $input.val("").trigger("change");
        $player.remove();
        if (audioUppy) {
          audioUppy.reset();
        }
      });

      $player.append($audio).append($remove);
      $input.after($player);
    }

    /* ========================================
           INITIALISATION AUTOMATIQUE
        ======================================== */

    // Détecter et initialiser les zones d'upload
    $(".mbaa-form").each(function () {
      const $form = $(this);

      // Vérifier si c'est un formulaire d'image
      if ($form.find('input[type="url"]#image_url').length) {
        console.log("Initialisation Uppy Image");
        initImageUppy($form);
      }

      // Vérifier si c'est un formulaire audio
      if (
        $form.find('input[type="url"]#audio_url').length ||
        $form.find('input[type="url"]#audio_biographie').length
      ) {
        console.log("Initialisation Uppy Audio");
        initAudioUppy($form);
      }
    });

    /* ========================================
           NAVIGATION & AUTRES FONCTIONNALITÉS
        ======================================== */

    // Gestion des boutons de navigation
    $(".mbaa-nav-button").on("click", function (e) {
      if ($(this).attr("href")) {
        return true;
      }
      $(".mbaa-nav-button").removeClass("active");
      $(this).addClass("active");
      e.preventDefault();
    });

    // Confirmation de suppression
    $(".submitdelete").on("click", function (e) {
      if (!confirm("Êtes-vous sûr de vouloir supprimer cet élément ?")) {
        e.preventDefault();
        return false;
      }
    });

    // Validation de formulaire
    $('form[id$="-form"]').on("submit", function (e) {
      var isValid = true;
      var firstError = null;

      $(this)
        .find("[required]")
        .each(function () {
          if (!$(this).val() || $(this).val().trim() === "") {
            isValid = false;
            $(this).addClass("error");
            if (!firstError) {
              firstError = $(this);
            }
          } else {
            $(this).removeClass("error");
          }
        });

      if (!isValid) {
        e.preventDefault();
        var message = "Veuillez remplir tous les champs obligatoires.";
        if (typeof mbaaToast !== "undefined") {
          mbaaToast.error(message);
        } else {
          alert(message);
        }
        if (firstError) {
          $("html, body").animate(
            { scrollTop: firstError.offset().top - 100 },
            300
          );
          firstError.focus();
        }
        return false;
      }
    });

    // Supprimer la classe error au focus
    $(".mbaa-form-input, .mbaa-form-select, .mbaa-form-textarea").on(
      "focus",
      function () {
        $(this).removeClass("error");
      }
    );

    // Auto-dismiss notifications
    setTimeout(function () {
      $(".notice.is-dismissible").fadeOut(300, function () {
        $(this).remove();
      });
    }, 5000);

    // Character counter
    $("textarea[data-max-chars]").each(function () {
      var $textarea = $(this);
      var maxChars = parseInt($textarea.data("max-chars"));
      var $counter = $(
        '<div class="mbaa-char-counter" style="font-size: 12px; color: #737373; margin-top: 6px;">0 / ' +
          maxChars +
          " caractères</div>"
      );
      $textarea.after($counter);

      $textarea.on("input", function () {
        var length = $(this).val().length;
        $counter.text(length + " / " + maxChars + " caractères");
        if (length > maxChars) {
          $counter.addClass("error").css("color", "#ef4444");
        } else {
          $counter.removeClass("error").css("color", "#737373");
        }
      });
    });

    // Confirmation before unload
    var formChanged = false;
    $(".mbaa-form :input").on("change input", function () {
      formChanged = true;
    });
    $(".mbaa-form").on("submit", function () {
      formChanged = false;
    });
    $(window).on("beforeunload", function () {
      if (formChanged) {
        return "Vous avez des modifications non enregistrées. Voulez-vous vraiment quitter cette page ?";
      }
    });
  });
})(jQuery);
