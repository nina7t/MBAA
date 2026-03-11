const inputArtiste = document.getElementById('nom_artiste');
const suggestionsDiv = document.getElementById('suggestions');

// on ecoute quand lutilisateur tape quelque chose 

inputArtiste.addEventListener('input', function() {
    let recherche = this.value;
    
    // ensuite on fait une boucle pour 

    if (recherche.length >= 2) { // on commence par chercher après 2 lettres
        // on appelle le php (via fetch)
        fetch(mbaaAjax.ajaxurl + '?action=mbaa_recherche_artiste&nom=' + encodeURIComponent(recherche))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                afficherSuggestions(data.data);
            }
        });
    }

});

// maintenant on fait une fonction pour afficher les suggestions

function afficherSuggestions(artistes) {
    //on vide les anciennes suggestions 
    suggestionsDiv.innerHTML = '';

    // on cree une liste de propositions
    artistes.forEach(artiste => {
        let div = document.createElement('div');
        div.className = 'suggestion-item';
        div.textContent = artiste.nom;
        div.onclick = () => {
            inputArtiste.value = artiste.nom; // on remplit le champ au clic
            // Mettre à jour le champ select hidden avec l'ID
            const selectArtiste = document.getElementById('id_artiste');
            if (selectArtiste) {
                selectArtiste.value = artiste.id_artiste;
            }
            suggestionsDiv.innerHTML = ''; // on efface les suggestions 
        };
        suggestionsDiv.appendChild(div);
    });
}