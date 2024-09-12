// cpv-index_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        const modifyAllCpvBtn = document.getElementById('modifyAllCpvBtn');
        const modal = document.getElementById('cpvModal');
        const closeModal = document.getElementById('closeModal');
        const saveCpvAmount = document.getElementById('saveCpvAmount');

        // Ouvrir la modale lorsqu'on clique sur le bouton "Modifier"
        modifyAllCpvBtn.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        // Fermer la modale lorsqu'on clique sur la croix
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Fermer la modale lorsqu'on clique en dehors de celle-ci
        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        // Sauvegarder le nouveau montant pour tous les CPV
        saveCpvAmount.addEventListener('click', () => {
            const newAmount = document.getElementById('newCpvAmount').value;
            console.log(newAmount);
            // Envoie de la requête pour mettre à jour tous les CPV
            fetch('/cpv/update_all_cpv', {

                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ amount: newAmount })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();  // Actualiser la page après la mise à jour
                } else {
                    alert('Erreur lors de la mise à jour');
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
    }
}
