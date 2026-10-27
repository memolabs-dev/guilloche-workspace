<?php
require dirname(__DIR__).'/vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$kernel=new App\Kernel('test',true);
foreach(['overview','tokens','button','field','feedback','motion','pattern','unknown'] as $section) {
 $request=Symfony\Component\HttpFoundation\Request::create('/design-system?section='.$section);
 $response=$kernel->handle($request);
 if($response->getStatusCode()!==200 || !str_contains($response->getContent(),'studio--library')) throw new RuntimeException('FAIL '.$section);
 $kernel->terminate($request,$response);echo 'PASS system '.$section.PHP_EOL;
}
$request=Symfony\Component\HttpFoundation\Request::create('/design-system?section[]=x');
$response=$kernel->handle($request);
if($response->getStatusCode()!==400) throw new RuntimeException('Non scalar section');
echo 'PASS system non-scalar section rejected'.PHP_EOL;
$kernel->shutdown();
