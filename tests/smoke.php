<?php
require dirname(__DIR__).'/vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$kernel = new App\Kernel('test', true);
$checks = [
    ['/', 200, 'Bonjour Alexandre', null],
    ['/missions', 200, 'Rémunération du dirigeant', null],
    ['/missions?status=done', 200, '1 mission', 'Rémunération du dirigeant'],
    ['/missions?q=introuvable', 200, 'Aucune mission', null],
    ['/missions?q=%3Cscript%3E', 200, '&lt;script&gt;', '<script>'],
    ['/missions/1', 200, 'AR-2026-042', null],
    ['/missions/999', 404, null, null],
    ['/documents', 200, 'Votre bibliothèque', null],
    ['/documents/1', 200, 'Exemple fictif', null],
    ['/facturation', 200, 'Aucun paiement', null],
    ['/design-system', 200, 'Couleurs', null],
    ['/comprendre-la-stack', 200, 'Stimulus', null],
];
foreach ($checks as [$path, $status, $contains, $absent]) {
    $request = Symfony\Component\HttpFoundation\Request::create($path);
    if ($path === '/missions/1') $request->headers->set('Turbo-Frame', 'panel');
    $response = $kernel->handle($request);
    $html = $response->getContent();
    if (str_starts_with($path, '/missions?') && preg_match('/<turbo-frame id="mission-results"[^>]*>(.*?)<\/turbo-frame>/s', $html, $match)) {
        $html = $match[1];
    }
    if ($response->getStatusCode() !== $status || ($contains && !str_contains($html, $contains)) || ($absent && str_contains($html, $absent))) {
        fwrite(STDERR, 'FAIL '.$path.' HTTP '.$response->getStatusCode().PHP_EOL);
        exit(1);
    }
    $kernel->terminate($request, $response);
    echo 'PASS '.$path.PHP_EOL;
}
$kernel->shutdown();
