<?php
require dirname(__DIR__).'/vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$kernel = new App\Kernel('test', true);
$checks = [
    ['/missions?selected=1', 200, 'Valider la synthèse', 'data-selected="1"'],
    ['/missions?selected=5', 200, 'Aucun document associé', null],
    ['/missions?status=done&selected=1', 302, null, null],
    ['/missions?selected=999', 302, null, null],
    ['/missions?status=invalide', 200, 'Rémunération du dirigeant', null],
    ['/missions?q[]=x', 400, null, null],
    ['/missions?status[]=x', 400, null, null],
    ['/missions?selected[]=1', 400, null, null],
    ['/missions?q=introuvable', 200, 'Aucune mission trouvée', null],
    ['/missions/1', 200, 'Synthèse de votre situation', null],
    ['/missions/5', 200, 'Aucun document associé', null],
    ['/design-system', 200, 'Ui:Button', 'data-contract="merged"'],
];
foreach ($checks as [$path, $status, $contains, $attribute]) {
    foreach ([false, true] as $frame) {
        $request = Symfony\Component\HttpFoundation\Request::create($path);
        if ($frame) $request->headers->set('Turbo-Frame', 'mission-results');
        $response = $kernel->handle($request);
        $html = $response->getContent();
        if ($response->getStatusCode() !== $status || ($contains && !str_contains($html, $contains)) || ($attribute && !str_contains($html, $attribute))) {
            fwrite(STDERR, 'FAIL '.$path.' frame='.(int)$frame.' HTTP '.$response->getStatusCode().PHP_EOL); exit(1);
        }
        if ($path === '/missions?status=done&selected=1' && $response->headers->get('Location') !== '/missions?status=done') { throw new RuntimeException('Selection outside filter not canonicalized'); }
        $kernel->terminate($request, $response);
        echo 'PASS '.$path.' frame='.(int)$frame.PHP_EOL;
    }
}
$request = Symfony\Component\HttpFoundation\Request::create('/design-system');
$html = $kernel->handle($request)->getContent();
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
$xpath = new DOMXPath($dom);
$assert = static function (bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); echo 'PASS '.$message.PHP_EOL; };
$assert($xpath->query('//button[@data-contract="merged" and contains(@class,"ui-button") and contains(@class,"catalog-merged")]')->length === 1, 'component attributes/classes merged');
$assert($xpath->query('//button[@data-contract="merged" and not(@disabled) and not(@aria-busy)]')->length === 1, 'normal button enabled and not busy');
$assert($xpath->query('//button[@aria-busy="true" and @disabled]')->length === 1, 'loading button disabled');
$assert($xpath->query('//label[@for="sample-error"]')->length === 1 && $xpath->query('//input[@id="sample-error" and @aria-invalid="true" and @aria-describedby="sample-error-help sample-error-error"]')->length === 1, 'field label/help/error associated');
$assert($xpath->query('//span[contains(@class,"ui-status--neutral")]')->length === 1, 'unknown business status neutral');
$assert($xpath->query('//article//li[contains(@class,"document-item")]//a[@href="/documents/1"]')->length === 1, 'document row navigates to related fixture');
$assert($xpath->query('//*[@id="mission-title"]')->length === 1, 'detail title unique in catalogue');
$kernel->terminate($request, new Symfony\Component\HttpFoundation\Response());
$kernel->shutdown();
