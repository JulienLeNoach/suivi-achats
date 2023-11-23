import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {

        // this.redirect()
    }
    redirect() {
        var redirectButton = document.getElementById('redirectButton');
        var redirectUrl = redirectButton.dataset.redirectUrl; // Récupérer l'URL depuis l'attribut data

        redirectButton.addEventListener('click', function() {
            window.location.href = redirectUrl;
        });
    }   
}