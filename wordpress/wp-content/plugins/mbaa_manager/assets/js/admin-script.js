/**
 * MBAA Plugin - Admin JavaScript
 * Nouvelle interface moderne avec support drag & drop et validation
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    /* ========================================
           NAVIGATION & HEADER
        ======================================== */

    // Gestion des boutons de navigation
    $(".mbaa-nav-button").on("click", function (e) {
      // Si c'est un lien, ne pas empêcher la navigation
      if ($(this).attr("href")) {
        return true;
      }

      $(".mbaa-nav-button").removeClass("active");
      $(this).addClass("active");
      e.preventDefault();
    });

    /* ========================================
           FORMULAIRES MODERNES
        ======================================== */

    // Confirmation de suppression
    $(".submitdelete").on("click", function (e) {
      if (!confirm("Êtes-vous sûr de vouloir supprimer cet élément ?")) {
        e.preventDefault();
        return false;
      }
    });

    // Validation de formulaire moderne
    $('form[id$="-form"]').on("submit", function (e) {
      var isValid = true;
      var firstError = null;

      // Vérifier les champs requis
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

        // Message d'erreur personnalisé
        var message = "Veuillez remplir tous les champs obligatoires.";

        // Utiliser une notification toast si disponible
        if (typeof mbaaToast !== "undefined") {
          mbaaToast.error(message);
        } else {
          alert(message);
        }

        if (firstError) {
          $("html, body").animate(
            {
              scrollTop: firstError.offset().top - 100,
            },
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

    /* ========================================
           ZONES DE UPLOAD & DRAG & DROP
        ======================================== */

    // Gestion du téléchargement de médias (images, audio)
    if (typeof wp !== "undefined" && wp.media) {
      var mediaFrame;
      var audioFrame;

      // Bouton upload d'image
      $(".mbaa-form").on("click", "#upload_image_button", function (e) {
        e.preventDefault();
        var $button = $(this);
        var $input = $button.prev('input[type="url"]');

        if (mediaFrame) {
          mediaFrame.open();
          return;
        }

        mediaFrame = wp.media({
          title: "Choisir une image",
          button: {
            text: "Utiliser cette image",
          },
          library: {
            type: "image",
          },
          multiple: false,
        });

        mediaFrame.on("select", function () {
          var attachment = mediaFrame.state().get("selection").first().toJSON();
          $input.val(attachment.url).trigger("change");

          // Déclencher l'événement custom
          $input.trigger("mbaa:image-selected", [attachment]);
        });

        mediaFrame.open();
      });

      // Bouton upload audio
      $(".mbaa-form").on("click", "#upload_audio_button", function (e) {
        e.preventDefault();
        var $button = $(this);
        var $input = $button.prev('input[type="url"]');

        if (audioFrame) {
          audioFrame.open();
          return;
        }

        audioFrame = wp.media({
          title: "Choisir un fichier audio",
          button: {
            text: "Utiliser ce fichier",
          },
          library: {
            type: "audio",
          },
          multiple: false,
        });

        audioFrame.on("select", function () {
          var attachment = audioFrame.state().get("selection").first().toJSON();
          $input.val(attachment.url).trigger("change");

          // Déclencher l'événement custom
          $input.trigger("mbaa:audio-selected", [attachment]);
        });

        audioFrame.open();
      });
    }

    // Drag & Drop pour les zones d upload 
    $(".mbaa-upload-area").each(function () {
      var $area = $(this);
      var $parent = $area.closest(
        '[class*="mbaa-form"], [class*="mbaa-media"]'
      );
      var $input = $parent.find('input[type="url"]');

      // Click sur la zone
      $area.on("click", function () {
        if ($input.attr("id") === "image_url") {
          $("#upload_image_button").trigger("click");
        } else if (
          $input.attr("id") === "audio_url" ||
          $input.attr("id") === "audio_biographie"
        ) {
          $("#upload_audio_button").trigger("click");
        }
      });

      // Drag over
      $area.on("dragover", function (e) {
        e.preventDefault();
        $area.addClass("dragover");
      });

      // Drag leave
      $area.on("dragleave", function () {
        $area.removeClass("dragover");
      });

      // Drop
      $area.on("drop", function (e) {
        e.preventDefault();
        $area.removeClass("dragover");

        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
          var file = files[0];
          uploadFile(file, $input);
        }
      });
    });

    // Fonction d'upload de fichier
    function uploadFile(file, $input) {
      var isImage = file.type.match(/^image\//);
      var maxSize = isImage ? 10 * 1024 * 1024 : 50 * 1024 * 1024; // 10MB ou 50MB

      if (file.size > maxSize) {
        alert(
          "Le fichier est trop volumineux. Maximum: " +
            (isImage ? "10 MB" : "50 MB")
        );
        return;
      }

      var formData = new FormData();
      formData.append("file", file);
      formData.append("action", "mbaa_upload_media");
      formData.append("nonce", mbaaAjax.nonce || "");

      $.ajax({
        url: mbaaAjax.ajaxurl || ajaxurl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
          $input.addClass("mbaa-loading");
        },
        success: function (response) {
          $input.removeClass("mbaa-loading");

          if (response.success) {
            $input.val(response.data.url).trigger("change");

            // Afficher l'aperçu pour les images
            if (isImage) {
              var previewHtml =
                '<div class="mbaa-image-preview" style="margin-top: 16px;">' +
                '<img src="' +
                response.data.url +
                '" alt="Aperçu">' +
                "</div>";

              if ($input.next(".mbaa-image-preview").length === 0) {
                $input.after(previewHtml);
              } else {
                $input
                  .next(".mbaa-image-preview")
                  .html('<img src="' + response.data.url + '" alt="Aperçu">');
              }
            }

            // Notification de succès
            if (typeof mbaaToast !== "undefined") {
              mbaaToast.success("Fichier téléversé avec succès !");
            } else {
              alert("Fichier téléversé avec succès !");
            }
          } else {
            alert(
              "Erreur lors du téléversement: " +
                (response.data || "Erreur inconnue")
            );
          }
        },
        error: function () {
          $input.removeClass("mbaa-loading");
          alert("Erreur lors du téléversement.");
        },
      });
    }

    /* ========================================
           ACTION BUTTONS
        ======================================== */

    // Gestion des boutons d'action (partager, télécharger, supprimer)
    $(".mbaa-action-button").on("click", function (e) {
      var title = $(this).attr("title") || $(this).text();
      console.log("Action:", title);

      // Si c'est un bouton de suppression, la confirmation est gérée séparément
      if ($(this).hasClass("danger")) {
        return; // La confirmation est gérée inline
      }

      // Autres actions
      switch (title) {
        case "Partager":
          if (navigator.share) {
            navigator.share({
              title: document.title,
              url: window.location.href,
            });
          } else {
            // Copier l'URL dans le presse-papiers
            navigator.clipboard
              .writeText(window.location.href)
              .then(function () {
                alert("Lien copié dans le presse-papiers !");
              });
          }
          break;

        case "Télécharger":
          window.print();
          break;
      }
    });

    /* ========================================
           AUTO-DISMISS NOTIFICATIONS
        ======================================== */

    setTimeout(function () {
      $(".notice.is-dismissible").fadeOut(300, function () {
        $(this).remove();
      });
    }, 5000);

    /* ========================================
           CHARACTER COUNTER
        ======================================== */

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

    /* ========================================
           CONFIRMATION BEFORE UNLOAD
        ======================================== */

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

    /* ========================================
           TABLE SORTING
        ======================================== */

    $(".mbaa-sortable th").on("click", function () {
      var $th = $(this);
      var column = $th.data("column");
      var order = $th.hasClass("asc") ? "desc" : "asc";

      // Mettre à jour l'URL avec les paramètres de tri
      var url = new URL(window.location.href);
      url.searchParams.set("orderby", column);
      url.searchParams.set("order", order);

      window.location.href = url.toString();
    });

    /* ========================================
           TOGGLE SECTIONS
        ======================================== */

    $(".mbaa-section-toggle").on("click", function (e) {
      e.preventDefault();
      var $section = $(this).next(".mbaa-section-content");
      $section.slideToggle(300);
      $(this)
        .find(".dashicons")
        .toggleClass("dashicons-arrow-down dashicons-arrow-up");
    });

    /* ========================================
           TOOLTIPS
        ======================================== */

    $(".mbaa-tooltip").each(function () {
      $(this).attr("title", $(this).data("tooltip"));
    });

    /* ========================================
           AJAX SEARCH
        ======================================== */

    var searchTimeout;
    $(".mbaa-search-input").on("keyup", function () {
      clearTimeout(searchTimeout);
      var $input = $(this);
      var searchTerm = $input.val();

      searchTimeout = setTimeout(function () {
        if (searchTerm.length >= 3 || searchTerm.length === 0) {
          performSearch(searchTerm);
        }
      }, 500);
    });

    function performSearch(term) {
      $.ajax({
        url: mbaaAjax.ajaxurl,
        type: "POST",
        data: {
          action: "mbaa_search",
          term: term,
          nonce: mbaaAjax.nonce,
        },
        beforeSend: function () {
          $(".mbaa-search-results").addClass("mbaa-loading");
        },
        success: function (response) {
          $(".mbaa-search-results").removeClass("mbaa-loading");
          if (response.success) {
            displaySearchResults(response.data);
          }
        },
        error: function () {
          $(".mbaa-search-results").removeClass("mbaa-loading");
        },
      });
    }

    function displaySearchResults(results) {
      var $container = $(".mbaa-search-results");
      $container.empty();

      if (results.length === 0) {
        $container.html("<p>Aucun résultat trouvé.</p>");
        return;
      }

      var html = "<ul>";
      results.forEach(function (item) {
        html += '<li><a href="' + item.url + '">' + item.title + "</a></li>";
      });
      html += "</ul>";

      $container.html(html);
    }
  });
})(jQuery);
