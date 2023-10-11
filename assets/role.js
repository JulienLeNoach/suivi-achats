//Ce script écoute les changements de sélection d'une liste déroulante (select)
//et envoie une requête GET à l'URL "/get_role/{selectedValue}" pour récupérer
//les rôles d'un utilisateur spécifique. En fonction des rôles retournés, les cases
//à cocher correspondantes sont cochées ou décochées. De plus, lorsqu'un bouton de
//soumission (submitChangesBtn) est cliqué, une requête POST est envoyée à l'URL
//"/save_roles2" pour enregistrer les modifications des rôles de l'utilisateur sélectionné.
//Les rôles sélectionnés sont extraits des cases à cocher et envoyés dans le corps de la requête
//au format JSON. Les réponses des requêtes sont affichées dans l'élément avec l'ID "roleDiv".

document.addEventListener('DOMContentLoaded', () => {

    var select = document.getElementById('role_nom_connexion');
    var roleDiv = document.getElementById('role');

    select.addEventListener('change', function () {
        var selectedValue = this.value;
        var url = "/get_role/" + selectedValue;
        roleDiv.innerHTML = " ";

        var xhr = new XMLHttpRequest();
        xhr.open("GET", url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var userRoles = response.role; // Supposons que les rôles soient retournés sous forme de tableau



                    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                    for (var i = 0; i < checkboxes.length; i++) {
                        var checkbox = checkboxes[i];
                        var roleName = checkbox.name;

                        if (userRoles.includes(roleName)) {
                            checkbox.checked = true;
                        } else {
                            checkbox.checked = false;
                        }   
                    }
                    if ((userRoles.includes('ROLE_ADMIN'))|| (userRoles.includes('ROLE_SUPER_ADMIN'))) {
                        for (var i = 0; i < checkboxes.length; i++) {
                            var checkbox = checkboxes[i];
                            checkbox.checked = true;
                        }
                    }

                    if (userRoles.includes('ROLE_OPT_ENVIRONNEMENT')) {
                        document.getElementById('ROLE_OPT_CPV').checked = true;
                        document.getElementById('ROLE_OPT_FORMATIONS').checked = true;
                        document.getElementById('ROLE_OPT_FOURNISSEURS').checked = true;
                        document.getElementById('ROLE_OPT_UO').checked = true;

                    }
                    if (userRoles.includes('ROLE_OPT_ADMINISTRATION')) {
                        document.getElementById('ROLE_OPT_UTILISATEURS').checked = true;
                        document.getElementById('ROLE_OPT_SERVICES').checked = true;
                        document.getElementById('ROLE_OPT_PARAMETRES').checked = true;
                        document.getElementById('ROLE_OPT_DROITS').checked = true;

                    }
                    if ((userRoles.includes('ROLES_OPT_ACHATS')) || (userRoles.includes('ROLE_USER'))) {
                        document.getElementById('ROLE_OPT_SAISIR_ACHATS').checked = true;
                        document.getElementById('ROLE_OPT_RECHERCHE_ACHATS').checked = true;
                        document.getElementById('ROLE_OPT_ANNULER_ACHATS').checked = true;
                        document.getElementById('ROLE_OPT_MODIFIER_ACHATS').checked = true;
                        document.getElementById('ROLE_OPT_REINT_ACHATS').checked = true;
                        document.getElementById('ROLE_OPT_VALIDER_ACHATS').checked = true;
                    }
                    if (userRoles.includes('ROLE_OPT_STATISTIQUES')) {
                        document.getElementById('ROLE_OPT_ACTIV_ANNUEL').checked = true;
                        document.getElementById('ROLE_OPT_CR_ANNUEL').checked = true;
                        document.getElementById('ROLE_OPT_CUMUL_CPV').checked = true;
                        document.getElementById('ROLE_OPT_DELAI_ANNUEL').checked = true;
                        document.getElementById('ROLE_OPT_STAT_MPPA_MABC').checked = true;
                        document.getElementById('ROLE_OPT_STAT_PME').checked = true;
                        document.getElementById('ROLE_OPT_EXCTRACT_DONNEES').checked = true;

                    }

                } else {
                    console.log('Une erreur s\'est produite lors de la requête');
                }
            }
        };

        xhr.send();
    });

    var submitChangesBtn = document.getElementById('submitChangesBtn');
    submitChangesBtn.addEventListener('click', function () {
        var selectedValue = select.value;
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var selectedRoles = [];

        for (var i = 0; i < checkboxes.length; i++) {
            var checkbox = checkboxes[i];
            if (checkbox.checked) {
                selectedRoles.push(checkbox.name);
            }
        }

        var data = {
            selectedValue: selectedValue,
            selectedRoles: selectedRoles
        };
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/save_roles2", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Roles saved successfully.');
                    roleDiv.innerHTML = "Les droits d'accès ont bien été modifiés.";

                } else {
                    console.log('An error occurred while saving the roles.');
                }
            }
        };

        xhr.send(JSON.stringify(data));
    });
});