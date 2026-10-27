<?php
require dirname(__DIR__).'/vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$kernel = new App\Kernel('test', true);
$checks = [
    ['/documents', 200, 'Rechercher un document', 'Filtrer par mission', null],
    ['/documents?mission=2', 200, 'Lettre de mission', 'Présentation de votre entreprise', 'Synthèse de votre situation'],
    ['/documents?mission=5', 200, 'Aucun document trouvé', null, 'document-item--library'],
    ['/documents?q=contrat', 200, 'Lettre de mission', null, 'Synthèse de votre situation'],
    ['/documents?q=introuvable', 200, 'Aucun document trouvé', null, null],
    ['/documents?mission=999&q=synthèse', 302, null, null, null],
    ['/documents?mission[]=2', 400, null, null, null],
    ['/documents?q=%3Cscript%3E', 200, '&lt;script&gt;', null, '<script>'],
    ['/documents/1?mission[]=1', 400, null, null, null],
    ['/documents?q[]=x', 400, null, null, null],
    ['/documents/1?q=synthèse&mission=1', 200, '/documents?q=synth%C3%A8se&amp;mission=1', 'Retour aux documents', null],
    ['/documents/1?mission=999', 200, 'Retour aux documents', null, 'mission=999'],
    ['/documents/999', 404, null, null, null],
];
foreach ($checks as [$path,$status,$contains,$also,$absent]) {
    foreach ([false,true] as $frame) {
        $request=Symfony\Component\HttpFoundation\Request::create($path);
        if($frame) $request->headers->set('Turbo-Frame','document-results');
        $response=$kernel->handle($request); $html=$response->getContent();
        if ($response->getStatusCode()!==$status || ($contains && !str_contains($html,$contains)) || ($also && !str_contains($html,$also)) || ($absent && str_contains($html,$absent))) {
            fwrite(STDERR,'FAIL '.$path.' frame='.(int)$frame.' HTTP '.$response->getStatusCode().PHP_EOL); exit(1);
        }
        if($path==='/documents?mission=999&q=synthèse' && $response->headers->get('Location')!=='/documents?q=synth%C3%A8se') throw new RuntimeException('Non canonical mission filter');
        $kernel->terminate($request,$response); echo 'PASS '.$path.' frame='.(int)$frame.PHP_EOL;
    }
}
$kernel->shutdown();
