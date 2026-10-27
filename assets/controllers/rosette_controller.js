import { Controller } from '@hotwired/stimulus';

// ui--rosette : dessine une rosette guillochee deterministe a partir d'une
// graine texte. Une epitrochoide normalisee, plusieurs copies tissees.
// Le trace de repli reste visible sans JavaScript.
export default class extends Controller {
    static values = { seed: String, size: Number, progress: Number, ssr: Boolean };

    connect() {
        const main = this.element.querySelector('.ui-rosette__main');
        const counter = this.element.querySelector('.ui-rosette__counter');
        if (!main || !counter) return;

        if (!this.ssrValue) {
            const parameters = this.parameters(this.seedValue || 'guilloche');
            main.setAttribute('d', this.trace(parameters));
            counter.setAttribute('d', this.trace({ ...parameters, petals: parameters.petals + 2, phase: parameters.phase + Math.PI / parameters.copies }));
        }

        const progress = Number.isFinite(this.progressValue) ? Math.min(100, Math.max(0, this.progressValue)) : 100;
        if (!this.ssrValue && progress < 100) {
            const length = main.getTotalLength();
            main.style.strokeDasharray = String(length);
            main.style.strokeDashoffset = String(length * (1 - progress / 100));
        }
    }

    hash(text) {
        let value = 2166136261;
        for (const character of text) {
            value ^= character.codePointAt(0);
            value = Math.imul(value, 16777619);
        }
        return value >>> 0;
    }

    parameters(seed) {
        const h = this.hash(seed);
        return {
            petals: 3 + (h % 3) * 2,
            copies: 1 + ((h >>> 4) % 2),
            phase: ((h >>> 8) % 360) * (Math.PI / 180),
            skew: 0.8 + ((h >>> 13) % 40) / 100,
        };
    }

    trace({ petals, copies, phase, skew }) {
        const steps = 360;
        const points = [];
        for (let copy = 0; copy < copies; copy++) {
            const rotation = phase + (copy * 2 * Math.PI) / copies;
            for (let step = 0; step <= steps; step++) {
                const t = (step / steps) * 2 * Math.PI;
                const radius = 45 * Math.cos(petals * t);
                const x = radius * Math.cos(t + rotation) * skew;
                const y = radius * Math.sin(t + rotation);
                points.push(`${step === 0 ? 'M' : 'L'}${(50 + x).toFixed(2)} ${(50 + y).toFixed(2)}`);
            }
        }
        return points.join(' ');
    }
}
