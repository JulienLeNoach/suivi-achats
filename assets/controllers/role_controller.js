import { Controller } from '@hotwired/stimulus';

const select = document.getElementById('role_nom_connexion');
const roleDiv = document.getElementById('role');
const submitChangesBtn = document.getElementById('submitChangesBtn');
const checkboxes = document.querySelectorAll('input[type="checkbox"]');
export default class extends Controller {
    
    static targets = ["selectAllContainer", "checkbox"];
    connect() {
        document.querySelector('form').addEventListener('submit', this.handleFormSubmit);

        // Gestion de l'affichage de la checkbox "Tout cocher/décocher"
        const selectElement = document.getElementById('role_nom_connexion');
        selectElement.addEventListener('change', this.toggleSelectAllVisibility.bind(this));

        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.addEventListener('change', this.toggleAllCheckboxes.bind(this));
    }

    handleFormSubmit(event) {
        // Empêcher la soumission par défaut du formulaire
        event.preventDefault();
    }
    toggleSelectAllVisibility(event) {
        const selectAllContainer = this.selectAllContainerTarget;

        if (event.target.value) {
            selectAllContainer.style.display = "flex";
        } else {
            selectAllContainer.style.display = "none";
        }
    }
    toggleAllCheckboxes(event) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const selectAll = event.target.checked;

        checkboxes.forEach(checkbox => {
            if (checkbox !== event.target) { // Ne pas cocher/décocher la case "Sélectionner tout" elle-même
                checkbox.checked = selectAll;
            }
        });
    }

    getRoles = async (event) => {
        const selectedValue = event.target.value; // Utilisez event.target pour obtenir la valeur sélectionnée
        console.log("selectedValue: " + selectedValue);
        const url = `/get_role/${selectedValue}`;

        try {
            const response = await fetch(url);
            if (response.ok) {
                const data = await response.json();
                this.updateCheckboxes(data.role);
            } else {
                console.error('Erreur réseau');
            }
        } catch (error) {
            console.error('Une erreur s\'est produite lors de la requête : ' + error);
        }
    }
    updateCheckboxes(userRoles){
        if (!Array.isArray(userRoles)) {
            // Si ce n'est pas un tableau, essayez de le convertir en tableau
            userRoles = Array.from(userRoles);
        }
        checkboxes.forEach(checkbox => {
            const roleName = checkbox.name;
            checkbox.checked = userRoles.includes(roleName);
        });
        
        if (userRoles.includes('ROLE_ADMIN') || userRoles.includes('ROLE_SUPER_ADMIN')) {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }
        
        if (userRoles.includes('ROLE_OPT_ENVIRONNEMENT')) {
            ['ROLE_OPT_CPV', 'ROLE_OPT_FORMATIONS', 'ROLE_OPT_FOURNISSEURS', 'ROLE_OPT_UO'].forEach(role => {
                document.getElementById(role).checked = true;
            });
        }
        
        if (userRoles.includes('ROLE_OPT_ADMINISTRATION')) {
            ['ROLE_OPT_UTILISATEURS', 'ROLE_OPT_SERVICES', 'ROLE_OPT_PARAMETRES', 'ROLE_OPT_DROITS'].forEach(role => {
                document.getElementById(role).checked = true;
            });
        }
        
        if (userRoles.includes('ROLES_OPT_ACHATS') || userRoles.includes('ROLE_USER')) {
            ['ROLE_OPT_SAISIR_ACHATS', 'ROLE_OPT_RECHERCHE_ACHATS', 'ROLE_OPT_ANNULER_ACHATS', 'ROLE_OPT_MODIFIER_ACHATS', 'ROLE_OPT_REINT_ACHATS', 'ROLE_OPT_VALIDER_ACHATS'].forEach(role => {
                document.getElementById(role).checked = true;
            });
        }
        
        if (userRoles.includes('ROLE_OPT_STATISTIQUES')) {
            ['ROLE_OPT_ACTIV_ANNUEL', 'ROLE_OPT_CR_ANNUEL', 'ROLE_OPT_CUMUL_CPV', 'ROLE_OPT_DELAI_ANNUEL', 'ROLE_OPT_STAT_MPPA_MABC', 'ROLE_OPT_STAT_PME', 'ROLE_OPT_EXCTRACT_DONNEES'].forEach(role => {
                document.getElementById(role).checked = true;
            });
        }
    }



    saveRoles() {
        const selectedValue = select.value;
        const selectedRoles = Array.from(checkboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.name);
        
        const data = {
            selectedValue: selectedValue,
            selectedRoles: selectedRoles
        };
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/save_roles2', true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Roles saved successfully.');
                    roleDiv.innerHTML = 'Les droits d\'accès ont bien été modifiés.';
                } else {
                    console.log('An error occurred while saving the roles.');
                }
            }
        };
        
        xhr.send(JSON.stringify(data));
    }
    

}