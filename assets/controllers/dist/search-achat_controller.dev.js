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
    }
  }, {
    key: "collapse",
    value: function collapse() {
      var formContainer = document.querySelector('.form-container');
      var toggleButton = document.getElementById('toggleFormBtn');
      formContainer.classList.add('collapsed');
      toggleButton.innerHTML = '<span class="fr-icon-arrow-down-fill" aria-hidden="true"></span>';
      toggleButton.addEventListener('click', function () {
        if (formContainer.classList.contains('collapsed')) {
          formContainer.classList.remove('collapsed');
          toggleButton.innerHTML = '<span class="fr-icon-arrow-up-fill" aria-hidden="true"></span>';
        } else {
          formContainer.classList.add('collapsed');
          toggleButton.innerHTML = '<span class="fr-icon-arrow-down-fill" aria-hidden="true"></span>';
        }
      });
    }
  }, {
    key: "attachEventListeners",
    value: function attachEventListeners() {
      var table = document.querySelector('table');
      var footer = document.querySelector('footer');
      var noResult = document.querySelector('#noResult'); // Vérifier si la table existe

      table ? footer.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      }) : noResult ? noResult.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      }) : null;
      var rows = document.querySelectorAll('.clickable-row');
      var btnElements = document.querySelectorAll('#btn');
      rows.forEach(function (row) {
        row.addEventListener('click', function () {
          btnElements.forEach(function (btn) {
            btn.removeAttribute('disabled');
          });
          rows.forEach(function (otherRow) {
            otherRow.classList.remove('selected');
          });
          var etatCell = row.cells[7];
          var etatAchatText = etatCell.textContent.replace(/\s+/g, '');
          var selectedRow = document.querySelector('.selected');

          if (etatAchatText == 'Validé') {
            document.querySelectorAll('.valid, .reint').forEach(function (el) {
              return el.setAttribute('disabled', 'disabled');
            });
          } else if (etatAchatText == 'Encours') {
            document.querySelectorAll('.reint').forEach(function (el) {
              return el.setAttribute('disabled', 'disabled');
            });
          } else if (etatAchatText = 'Annulé') {
            document.querySelectorAll('.annul, .valid, .edit').forEach(function (el) {
              return el.setAttribute('disabled', 'disabled');
            });
          }

          row.classList.add('selected');
          btnElements.forEach(function (btn) {
            btn.addEventListener('click', function () {
              // Récupérer le lien et l'ID
              var link = btn.getAttribute('data-link');
              var action = btn.getAttribute('data-action');
              var id = document.querySelector('.selected').getAttribute('data-id'); // Construire le message d'alerte
              // let confirmationMessage = `Voulez-vous vraiment ${action} cet achat?`;
              // Afficher l'alerte et rediriger si l'utilisateur confirme
              // if (confirm(confirmationMessage)) {

              console.log("confirm");
              var detailLink = document.getElementById('detail');
              detailLink.setAttribute('href', '/' + link + '/' + id);
              window.location.href = detailLink.getAttribute('href'); // }
            });
          });
        });
      });
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;