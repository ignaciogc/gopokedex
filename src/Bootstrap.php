<?php

namespace GoPokedex;

require __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

error_reporting(E_ALL);

$environment = 'development';

define("ROOT_PATH", __DIR__);
define("CONFIG", Yaml::parse(file_get_contents(__DIR__.'/config.yml'))[$environment], true);
$dbConfig = CONFIG['database'];

/**
* Register the error handler
*/
$whoops = new \Whoops\Run;
if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
        echo 'Todo: Friendly error page and send an email to the developer';
    });
}
$whoops->register();

/**
* Register HTTP handling
*/
$injector = include('Dependencies.php');

$request = $injector->make('Http\HttpRequest');
$response = $injector->make('Http\HttpResponse');

// Define Template used in route handling
$template = $injector->make('GoPokedex\System\Template');

// Define Database to use and share it via injector
$db = \Delight\Db\PdoDatabase::fromDsn(
    new \Delight\Db\PdoDsn(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset{$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password']
    )
);
$injector->defineParam('database', $db);

// Define authentication and share it via injector
$authentication = new \Delight\Auth\Auth($db);
$injector->defineParam('auth', $authentication);

/**
* Register routes
*/
$routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
    $routes = include('Routes.php');
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};

$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page not found');
        $response->setStatusCode(404);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];

        $class = $injector->make($className);
        $class->$method($vars);
        break;
}


/**
* Output headers + file_get_content
*/

foreach ($response->getHeaders() as $header) {
    header($header, false);
}

echo $response->getContent();
