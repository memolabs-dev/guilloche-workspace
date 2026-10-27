import { startStimulusApp } from '@symfony/stimulus-bridge';

// Enregistre les contrôleurs de controllers.json et du dossier controllers/, chargés à la demande.
export const app = startStimulusApp(import.meta.webpackContext('@symfony/stimulus-bridge/lazy-controller-loader!./controllers', {
    recursive: true,
    regExp: /\.[jt]sx?$/,
}));
