import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    currentStep = null;
    steps = [];

    initialize() {
        this.steps = document.getElementsByClassName('tutorialStep');
    }

    start() {
        console.log('start');
        this.currentStep = 0;
        this.update();
    }

    next() {
        this.currentStep++;
        this.update();
    }

    prev() {
        this.currentStep--;
        this.update();
    }

    close() {
        this.currentStep = null;
        this.update();
    }

    update() {
        for (let step of this.steps) {
            step.classList.add('hidden');
            step.classList.remove('block');
        }

        const step = this.steps[this.currentStep];
        if (step === undefined) {
            this.currentStep = null;
        } else {
            step.classList.remove('hidden');
            step.classList.add('block');
            step.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
    }
}
