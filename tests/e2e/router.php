<?php

// Serveur PHP integre des tests E2E : les fichiers publics existants sont
// servis tels quels, tout le reste passe par le controleur frontal Symfony.
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url(is_string($uri) ? $uri : '/', PHP_URL_PATH) ?: '/';
$file = __DIR__.'/../../public'.$path;

if ('cli-server' === php_sapi_name() && '/' !== $path && is_file($file)) {
    return false;
}

require __DIR__.'/../../public/index.php';
