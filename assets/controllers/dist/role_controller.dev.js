"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _stimulus = require("@hotwired/stimulus");

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var select = document.getElementById('role_nom_connexion');
var roleDiv = document.getElementById('role');
var submitChangesBtn = document.getElementById('submitChangesBtn');
var checkboxes = document.querySelectorAll('input[type="checkbox"]');

var _default =
/*#__PURE__*/
function (_Controller) {
  _inherits(_default, _Controller);

  function _default() {
    _classCallCheck(this, _default);

    return _possibleConstructorReturn(this, _getPrototypeOf(_default).apply(this, arguments));
  }

  _createClass(_default, [{
    key: "connect",
    value: function connect() {// select.addEventListener('change', function () {
      //     const selectedValue = this.value;
      //     roleDiv.innerHTML = " ";
      //     console.log("sV"+selectedValue);
      //     getRoles(selectedValue)
      // });
      // Écouteur d'événement pour le bouton de soumission
      // submitChangesBtn.addEventListener('click', saveRoles);
    }
  }, {
    key: "getRoles",
    value: function getRoles(selectedValue) {
      var _this = this;

      console.log(selectedValue);
      var url = "/get_role/110";
      console.log("svgr" + url);
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);

      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var userRoles = response.role;
            console.log(userRoles);

            _this.updateCheckboxes(userRoles);
          } else {
            console.log('Une erreur s\'est produite lors de la requête');
          }
        }
      };

      xhr.send();
    }
  }, {
    key: "updateCheckboxes",
    value: function updateCheckboxes(userRoles) {
      if (!Array.isArray(userRoles)) {
        // Si ce n'est pas un tableau, essayez de le convertir en tableau
        userRoles = Array.from(userRoles);
      }

      checkboxes.forEach(function (checkbox) {
        var roleName = checkbox.name;
        checkbox.checked = userRoles.includes(roleName);
      });

      if (userRoles.includes('ROLE_ADMIN') || userRoles.includes('ROLE_SUPER_ADMIN')) {
        checkboxes.forEach(function (checkbox) {
          checkbox.checked = true;
        });
      }

      if (userRoles.includes('ROLE_OPT_ENVIRONNEMENT')) {
        ['ROLE_OPT_CPV', 'ROLE_OPT_FORMATIONS', 'ROLE_OPT_FOURNISSEURS', 'ROLE_OPT_UO'].forEach(function (role) {
          document.getElementById(role).checked = true;
        });
      }

      if (userRoles.includes('ROLE_OPT_ADMINISTRATION')) {
        ['ROLE_OPT_UTILISATEURS', 'ROLE_OPT_SERVICES', 'ROLE_OPT_PARAMETRES', 'ROLE_OPT_DROITS'].forEach(function (role) {
          document.getElementById(role).checked = true;
        });
      }

      if (userRoles.includes('ROLES_OPT_ACHATS') || userRoles.includes('ROLE_USER')) {
        ['ROLE_OPT_SAISIR_ACHATS', 'ROLE_OPT_RECHERCHE_ACHATS', 'ROLE_OPT_ANNULER_ACHATS', 'ROLE_OPT_MODIFIER_ACHATS', 'ROLE_OPT_REINT_ACHATS', 'ROLE_OPT_VALIDER_ACHATS'].forEach(function (role) {
          document.getElementById(role).checked = true;
        });
      }

      if (userRoles.includes('ROLE_OPT_STATISTIQUES')) {
        ['ROLE_OPT_ACTIV_ANNUEL', 'ROLE_OPT_CR_ANNUEL', 'ROLE_OPT_CUMUL_CPV', 'ROLE_OPT_DELAI_ANNUEL', 'ROLE_OPT_STAT_MPPA_MABC', 'ROLE_OPT_STAT_PME', 'ROLE_OPT_EXCTRACT_DONNEES'].forEach(function (role) {
          document.getElementById(role).checked = true;
        });
      }
    }
  }, {
    key: "saveRoles",
    value: function saveRoles() {
      var selectedValue = select.value;
      var selectedRoles = Array.from(checkboxes).filter(function (checkbox) {
        return checkbox.checked;
      }).map(function (checkbox) {
        return checkbox.name;
      });
      var data = {
        selectedValue: selectedValue,
        selectedRoles: selectedRoles
      };
      var xhr = new XMLHttpRequest();
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
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;