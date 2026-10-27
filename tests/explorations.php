<?php
require dirname(__DIR__).'/vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$kernel=new App\Kernel('test',true);
foreach(['priorites','atelier','panorama'] as $direction) {
 foreach ([['?selected=1',200,'Valider la synthèse'],['?q=introuvable',200,'Aucune mission trouvée'],['?status=done&selected=1',302,null],['?q[]=x',400,null],['?selected[]=1',400,null],['?selected=5',200,'Aucun document associé']] as [$query,$status,$contains]) {
  $request=Symfony\Component\HttpFoundation\Request::create('/explorations/missions/'.$direction.$query);
  $request->headers->set('Turbo-Frame','exploration-results');
  $response=$kernel->handle($request);
  if($response->getStatusCode()!==$status || ($contains && !str_contains($response->getContent(),$contains))) throw new RuntimeException('FAIL '.$direction.$query.' HTTP '.$response->getStatusCode());
  $kernel->terminate($request,$response);echo 'PASS '.$direction.$query.PHP_EOL;
 }
}
$kernel->shutdown();
