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
    value: function connect() {
      this.attachEventListeners();
      this.colorizeOptions(); // Appel pour appliquer la colorisation initialement
    }
  }, {
    key: "attachEventListeners",
    value: function attachEventListeners() {
      var rows = document.querySelectorAll('.clickable-row');
      var btnElements = document.querySelectorAll('#btn'); // Désactiver tous les boutons initialement

      btnElements.forEach(function (btn) {
        btn.setAttribute('disabled', 'disabled');
      });
      rows.forEach(function (row) {
        row.addEventListener('click', function () {
          // Activer les boutons lors de la sélection d'une ligne
          btnElements.forEach(function (btn) {
            btn.removeAttribute('disabled');
          }); // Désélectionner toutes les autres lignes

          rows.forEach(function (otherRow) {
            otherRow.classList.remove('selected');
          }); // Gérer l'état des boutons selon l'état de la ligne

          var etatCell = row.cells[7]; // Supposons que la 8ème cellule (index 7) contient l'état de l'achat

          var etatAchatText = etatCell.textContent.replace(/\s+/g, ''); // Supprimer les espaces

          if (etatAchatText === 'Validé') {
            document.querySelectorAll('.valid, .reint').forEach(function (el) {
              return el.setAttribute('disabled', 'disabled');
            });
          } else if (etatAchatText === 'Encours') {
            document.querySelectorAll('.reint').forEach(function (el) {
              return el.setAttribute('disabled', 'disabled');
            });
          } else if (etatAchatText === 'Annulé') {
            document.querySelectorAll('.annul, .valid, .edit').forEach(function (el) {
              return el.setAttribute('disabled', 'disabled');
            });
          } // Marquer la ligne sélectionnée


          row.classList.add('selected');
        });
      }); // Si aucune ligne n'est sélectionnée, tous les boutons restent désactivés

      btnElements.forEach(function (btn) {
        btn.setAttribute('disabled', 'disabled');
      });
      this.setupAlertForAnnulButton(btnElements);
      this.setupSaveComment();
    }
  }, {
    key: "setupAlertForAnnulButton",
    value: function setupAlertForAnnulButton(btnElements) {
      var _this = this;

      btnElements.forEach(function (btn) {
        if (!btn.hasListener) {
          btn.addEventListener('click', function (event) {
            if (btn.getAttribute('data-action') === 'annuler') {
              event.preventDefault();

              _this.showCommentModal(); // Afficher la modale pour le commentaire

            }

            var link = btn.getAttribute('data-link');
            var id = document.querySelector('.selected').getAttribute('data-id');
            document.getElementById('detail').setAttribute('href', '/' + link + '/' + id);
          });
          btn.hasListener = true;
        }
      });
    } // Fonction pour afficher la fenêtre modale

  }, {
    key: "showCommentModal",
    value: function showCommentModal() {
      var modal = document.getElementById('commentModal');
      modal.style.display = 'block';

      window.onclick = function (event) {
        if (event.target == modal) {
          modal.style.display = 'none';
        }
      };

      document.getElementById('closeModal').onclick = function () {
        modal.style.display = 'none';
      };
    } // Configuration pour envoyer le commentaire lors de l'annulation

  }, {
    key: "setupSaveComment",
    value: function setupSaveComment() {
      document.getElementById('saveComment').onclick = function () {
        var id = document.querySelector('.selected').getAttribute('data-id');
        var comment = document.getElementById('commentText').value; // Envoie la requête AJAX pour enregistrer le commentaire d'annulation

        fetch("/annul_achat/".concat(id), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json' // Assurez-vous que le serveur sait qu'il doit renvoyer du JSON

          },
          body: JSON.stringify({
            comment: comment
          })
        }).then(function (response) {
          // Vérifier que la réponse est bien du JSON
          if (!response.ok) {
            return response.text().then(function (text) {
              throw new Error(text); // Gérer les erreurs comme du texte si ce n'est pas du JSON
            });
          }

          return response.json(); // Si c'est du JSON, on le retourne
        }).then(function (data) {
          if (data.success) {
            window.location.href = data.redirectUrl; // Redirection après annulation
          } else {
            alert('Erreur lors de l\'annulation');
          }
        })["catch"](function (error) {
          console.error('Erreur:', error);
        });
      };
    }
  }, {
    key: "colorizeOptions",
    value: function colorizeOptions() {
      function colorizeOptions() {
        // Sélectionner tous les div avec le rôle option pour vérifier s'ils ont atteint le premier seuil
        var allDivs = document.querySelectorAll('div[role="option"]');
        allDivs.forEach(function (div) {
          var textContent = div.textContent || div.innerText; // Si le texte contient "Premier seuil atteint", coloriser en orange

          if (textContent.includes('Premier seuil atteint')) {
            div.style.color = 'orange'; // Coloriser en orange les éléments ayant atteint le premier seuil
          } else if (textContent.includes('Deuxieme seuil atteint')) {
            div.style.color = 'red';
          }
        });
      } // Observer les mutations dans le DOM pour détecter les changements


      var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
          if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
            colorizeOptions(); // Appeler la fonction lorsque des éléments sont ajoutés
          }
        });
      }); // Configurer l'observation du body pour suivre les changements dans l'arborescence DOM

      var config = {
        childList: true,
        subtree: true
      };
      observer.observe(document.body, config); // Appel initial pour coloriser les éléments déjà présents

      colorizeOptions();
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;