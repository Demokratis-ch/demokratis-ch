import { Controller } from '@hotwired/stimulus';

/*
 * Show, hide and toggle elements
 */
export default class extends Controller {
    static targets = [ "hideable" ]

    showTargets() {
        this.hideableTargets.forEach(el => {
            el.hidden = false;
            el.classList.remove("hidden");
            el.classList.add("block");
        });
    }

    hideTargets() {
        this.hideableTargets.forEach(el => {
            el.hidden = true;
            el.classList.remove("block");
            el.classList.add("hidden");
        });
    }

    toggleTargets() {
        this.hideableTargets.forEach((el) => {
            el.hidden = !el.hidden;
            el.classList.toggle("hidden");
            el.classList.toggle("block");
        });
    }
}
