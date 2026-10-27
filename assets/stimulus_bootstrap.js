import { Application } from '@hotwired/stimulus';

// Contrôleurs Stimulus enregistrés explicitement : import.meta.glob n'est pas
// supporté par les navigateurs et aucun bundler ne le résout ici.
import ShellController from './controllers/shell_controller.js';
import ThemeController from './controllers/theme_controller.js';
import CatalogController from './controllers/catalog_controller.js';
import FilteredResultsController from './controllers/filtered_results_controller.js';

const application = Application.start();

application.register('shell', ShellController);
application.register('theme', ThemeController);
application.register('catalog', CatalogController);
application.register('filtered-results', FilteredResultsController);

export const app = application;
