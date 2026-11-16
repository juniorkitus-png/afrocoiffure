import { Controller } from '@hotwired/stimulus';

/*
 * Menu mobile : ouvre / ferme la navigation sous 900 px.
 * Utilisation : data-controller="menu" sur l'en-tête,
 * data-menu-target="panel" sur la nav, data-action="menu#toggle" sur le bouton.
 */
export default class extends Controller {
    static targets = ['panel', 'button'];

    connect() {
        this.onScroll = () => this.element.classList.toggle('is-scrolled', window.scrollY > 8);
        window.addEventListener('scroll', this.onScroll, { passive: true });
        this.onScroll();
    }

    disconnect() {
        window.removeEventListener('scroll', this.onScroll);
    }

    toggle() {
        const open = !this.panelTarget.classList.contains('is-open');
        this.panelTarget.classList.toggle('is-open', open);
        this.buttonTarget.setAttribute('aria-expanded', String(open));
        this.buttonTarget.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
    }

    // Échap ferme le menu
    close(event) {
        if (event.key === 'Escape' && this.panelTarget.classList.contains('is-open')) {
            this.toggle();
            this.buttonTarget.focus();
        }
    }
}
