import { Controller } from '@hotwired/stimulus';

/*
 * Bouton « Afficher le mot de passe ».
 * Utilisation : data-controller="password-toggle" sur un conteneur,
 * data-password-toggle-target="input" sur chaque champ mot de passe,
 * data-action="password-toggle#toggle" sur le bouton.
 */
export default class extends Controller {
    static targets = ['input', 'button'];

    toggle() {
        const show = this.inputTargets[0].type === 'password';
        this.inputTargets.forEach((input) => { input.type = show ? 'text' : 'password'; });
        this.buttonTarget.textContent = show ? 'Masquer le mot de passe' : 'Afficher le mot de passe';
        this.buttonTarget.setAttribute('aria-pressed', String(show));
    }
}
