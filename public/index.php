<?php

use App\Routes\AdminModule;
use App\Routes\BlogModule;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use function Http\Response\send;

require dirname(__DIR__) . '/vendor/autoload.php';


$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

$modules = [
    AdminModule::class,
    BlogModule::class
];

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(dirname(__DIR__) . '/config.php');
$container = $builder->build();

$app = new \Framework\App($container, $modules);

if (PHP_SAPI !== 'cli') {
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    send($response);
}
