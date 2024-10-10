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
        
            // Vérification que l'utilisateur a bien entré une valeur
            if (newAmount === '') {
                alert('Veuillez entrer un montant.');
                return;
            }
        
            // Envoie de la requête pour mettre à jour tous les CPV
            fetch('/cpv/update_all_cpv', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ amount: newAmount })
            })
            .then(response => {
                // Ajout d'un contrôle pour s'assurer que la réponse est bien au format JSON
                if (!response.ok) {
                    throw new Error('Erreur serveur lors de la mise à jour.');
                }
                return response.json();
            })
            .then(data => {
                console.log('Réponse serveur:', data);  // Ajout d'un log pour vérifier la réponse
        
                if (data.success) {
                    // Recharger la page uniquement en cas de succès
                    window.location.reload();  
                } else {
                    alert('Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour');
            });
        });
    }
}
