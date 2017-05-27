<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */
namespace Kewodoa\Routing;
include '../vendor/autoload.php';



use Kewodoa\Routing\FactoryDirector;
use Kewodoa\Routing\Nested\NestedRouteFactory;
use Kewodoa\Routing\Nested\RouteRelay;
use Kewodoa\Routing\Route\SimplePatternResolver;
use Kewodoa\Routing\Simple\SimpleMatching;
use Kewodoa\Routing\Simple\SimpleRoute;
use Kewodoa\Routing\Simple\SimpleRouteFactory;
use Kewodoa\Routing\Simple\SimpleRouter;

$resolver   = new SimplePatternResolver();
$router     = new SimpleRouter($resolver);

$director = new FactoryDirector($router);

$director->setFactory(new NestedRouteFactory(),RouteRelay::class);
$director->setFactory(new SimpleRouteFactory(),SimpleRoute::class);
$director->setDefault(SimpleRoute::class);

//idea: pattern environment placeholders - Для подстановки данных из текущего окружения (например части запрашиваемого домена)
$route = $director->createRoute([
	'type'      => RouteRelay::class,
	'action'    => 'user:list',
	'pattern'   => '/users',
	//'symbolic'  => true, // or 'phantom' => true,
	'children'  => [[
		'action'    => 'user:create',
		'pattern'   => '/create',
	],[
		'type'      => RouteRelay::class,
		'reference' => 'user:view',
		'pattern'   => '/(?<uid>\d+)',
		'children'  => [[
			'action'    => 'user:delete',
			'pattern'   => '/delete',
		],[
			'action'    => 'user:update',
			'pattern'   => '/update',
		]],
	]]
]);


$p = '/users/91/notes/87/update';
$p1 = '/users/91/ba';
$matching = new SimpleMatching($p1);

$matching = $route->match($matching);


$reached = $matching->isReached();