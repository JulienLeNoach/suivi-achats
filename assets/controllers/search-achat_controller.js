import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.attachEventListeners();
        this.colorizeOptions(); // Appel pour appliquer la colorisation initialement

    }

    attachEventListeners() {
        let rows = document.querySelectorAll('.clickable-row');
        let btnElements = document.querySelectorAll('#btn');
    
        // Désactiver tous les boutons initialement
        btnElements.forEach((btn) => {
            btn.setAttribute('disabled', 'disabled');
        });
    
        rows.forEach((row) => {
            row.addEventListener('click', function () {
                // Activer les boutons lors de la sélection d'une ligne
                btnElements.forEach(function (btn) {
                    btn.removeAttribute('disabled');
                });
    
                // Désélectionner toutes les autres lignes
                rows.forEach(function (otherRow) {
                    otherRow.classList.remove('selected');
                });
    
                // Gérer l'état des boutons selon l'état de la ligne
                let etatCell = row.cells[7]; // Supposons que la 8ème cellule (index 7) contient l'état de l'achat
                let etatAchatText = etatCell.textContent.replace(/\s+/g, ''); // Supprimer les espaces
    
                if (etatAchatText === 'Validé') {
                    document.querySelectorAll('.valid, .reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                } else if (etatAchatText === 'Encours') {
                    document.querySelectorAll('.reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                } else if (etatAchatText === 'Annulé') {
                    document.querySelectorAll('.annul, .valid, .edit').forEach(el => el.setAttribute('disabled', 'disabled'));
                }
    
                // Marquer la ligne sélectionnée
                row.classList.add('selected');
            });
        });
    
        // Si aucune ligne n'est sélectionnée, tous les boutons restent désactivés
        btnElements.forEach(function (btn) {
            btn.setAttribute('disabled', 'disabled');
        });
    
        this.setupAlertForAnnulButton(btnElements);
        this.setupSaveComment();
    }
    
    setupAlertForAnnulButton(btnElements) {
        btnElements.forEach((btn) => {
            if (!btn.hasListener) {
                btn.addEventListener('click', (event) => {
                    if (btn.getAttribute('data-action') === 'annuler') {
                        event.preventDefault();
                        this.showCommentModal(); // Afficher la modale pour le commentaire
                    }

                    let link = btn.getAttribute('data-link');
                    let id = document.querySelector('.selected').getAttribute('data-id');
                    document.getElementById('detail').setAttribute('href', '/' + link + '/' + id);
                });

                btn.hasListener = true;
            }
        });
    }

    // Fonction pour afficher la fenêtre modale
    showCommentModal() {
        const modal = document.getElementById('commentModal');
        modal.style.display = 'block';

        window.onclick = (event) => {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };

        document.getElementById('closeModal').onclick = () => {
            modal.style.display = 'none';
        };
    }

    // Configuration pour envoyer le commentaire lors de l'annulation
    setupSaveComment() {
        document.getElementById('saveComment').onclick = () => {
            const id = document.querySelector('.selected').getAttribute('data-id');
            const comment = document.getElementById('commentText').value;

            // Envoie la requête AJAX pour enregistrer le commentaire d'annulation
            fetch(`/annul_achat/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',  // Assurez-vous que le serveur sait qu'il doit renvoyer du JSON
                },
                body: JSON.stringify({ comment: comment })
            })
            .then(response => {
                // Vérifier que la réponse est bien du JSON
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);  // Gérer les erreurs comme du texte si ce n'est pas du JSON
                    });
                }
                return response.json();  // Si c'est du JSON, on le retourne
            })
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirectUrl;  // Redirection après annulation
                } else {
                    alert('Erreur lors de l\'annulation');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        };
    }
    colorizeOptions() {
        function colorizeOptions() {


            // Sélectionner tous les div avec le rôle option pour vérifier s'ils ont atteint le premier seuil
            const allDivs = document.querySelectorAll('div[role="option"]');

            // allDivs.forEach((div) => {
            //     const textContent = div.textContent || div.innerText;
            //     // Si le texte contient "Premier seuil atteint", coloriser en orange
            //     if (textContent.includes('Premier seuil atteint')) {
            //         div.style.color = 'orange'; // Coloriser en orange les éléments ayant atteint le premier seuil
            //     }
            //     else if (textContent.includes('Deuxieme seuil atteint')){
            //         div.style.color = 'red';
            //     }
            // });
        }

        // Observer les mutations dans le DOM pour détecter les changements
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    colorizeOptions(); // Appeler la fonction lorsque des éléments sont ajoutés
                }
            });
        });

        // Configurer l'observation du body pour suivre les changements dans l'arborescence DOM
        const config = { childList: true, subtree: true };
        observer.observe(document.body, config);

        // Appel initial pour coloriser les éléments déjà présents
        colorizeOptions();
    }
}
