import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.attachEventListeners();
    }

    attachEventListeners() {
        let rows = document.querySelectorAll('.clickable-row');
        let btnElements = document.querySelectorAll('#btn');

        rows.forEach((row) => {
            row.addEventListener('click', function () {
                btnElements.forEach(function (btn) {
                    btn.removeAttribute('disabled');
                });
                rows.forEach(function (otherRow) {
                    otherRow.classList.remove('selected');
                });

                let etatCell = row.cells[7];
                let etatAchatText = etatCell.textContent.replace(/\s+/g, '');

                if (etatAchatText === 'Validé') {
                    document.querySelectorAll('.valid, .reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                } else if (etatAchatText === 'Encours') {
                    document.querySelectorAll('.reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                } else if (etatAchatText === 'Annulé') {
                    document.querySelectorAll('.annul, .valid, .edit').forEach(el => el.setAttribute('disabled', 'disabled'));
                }

                row.classList.add('selected');
            });
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
}
