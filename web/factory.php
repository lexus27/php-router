<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */
namespace Kewodoa\Routing;
include '../vendor/autoload.php';


use Kewodoa\Routing\Hierarchical\ConjunctionFactory;
use Kewodoa\Routing\Hierarchical\ConjunctionRoute;
use Kewodoa\Routing\Simple\SimpleMatching;
use Kewodoa\Routing\Simple\SimplePatternResolver;
use Kewodoa\Routing\Simple\SimpleRoute;
use Kewodoa\Routing\Simple\SimpleRouteFactory;
use Kewodoa\Routing\Simple\SimpleRouter;

$resolver   = new SimplePatternResolver();
$router     = new SimpleRouter($resolver);

$director = new FactoryDirector($router);

$director->setFactory(new ConjunctionFactory(),ConjunctionRoute::class);
$director->setFactory(new SimpleRouteFactory(),SimpleRoute::class);
$director->setDefault(SimpleRoute::class);

//idea: pattern environment placeholders - Для подстановки данных из текущего окружения (например части запрашиваемого домена)

$rule_list = [
	[
		'when' => [
			['http.scheme' => 'http'],
		],
		'then' => [
			'redirect' => [
				'destination' => [
					'http.scheme' => 'https',
				],
				'status_code' => 304,
				'status_text' => 'Temporal'
			]
		]
	],
];

$route = $director->createRoute([
	'type'      => ConjunctionRoute::class,
	'action'    => 'user:list',
	'pattern'   => '/users',
	'attributes' => [
		['http.scheme','===','https'],
	],
	//'symbolic'  => true, // or 'phantom' => true,
	'children'  => [[
		'action'    => 'user:create',
		'pattern'   => '/create',
	],[
		'type'      => ConjunctionRoute::class,
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