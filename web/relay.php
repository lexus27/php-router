<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */


namespace Kewodoa\Routing;
include '../vendor/autoload.php';

use Kewodoa\Routing\Nested\RouteRelay;
use Kewodoa\Routing\Route\SimplePatternResolver;
use Kewodoa\Routing\Simple\SimpleMatching;
use Kewodoa\Routing\Simple\SimpleRoute;
use Kewodoa\Routing\Simple\SimpleRouter;

$resolver   = new SimplePatternResolver();
$router     = new SimpleRouter($resolver);

// Не забыть: В данный момент матч может возвращять иной объект Matching , в связи с необходимостью проведения живой совпавшей цепи для реализации Locations в будущем
$route = (new RouteRelay('user:list','/users'))
	->setRouter($router)
	->addRoute(
		(new SimpleRoute('user:create','/create'))->setRouter($router)
	)
	->addRoute(
		(new RouteRelay('user:view','/(?<uid>\d+)')) // matched destination on '/users/91'
		->setRouter($router)
			->addRoute( (new SimpleRoute('user:update', '/update'))->setRouter($router) )
			->addRoute( (new SimpleRoute('user:delete', '/delete'))->setRouter($router) )
			->addRoute(
				(new RouteRelay('user:note:list','/notes'))
					->setRouter($router)
					->addRoute(
						(new RouteRelay('user:note:view','/(?<note_id>\d+)'))
							->setRouter($router)
							->addRoute( (new SimpleRoute('user:note:update', '/update'))->setRouter($router) )
							->addRoute( (new SimpleRoute('user:note:delete', '/delete'))->setRouter($router) )
					)
					->addRoute(
						(new SimpleRoute('user:note:add', '/add'))->setRouter($router)
					)
					->addRoute(
						(new SimpleRoute('user:note:delete', '/delete'))->setRouter($router)
					)
			)
	)->addRoute(
		(new SimpleRoute('user:bo','/(?<babi>\d+)/ba'))->setRouter($router)
	);


$p = '/users/91/notes/87/update';
$p1 = '/users/91/ba';
$matching = new SimpleMatching($p1);

$matching = $route->match($matching);


$reached = $matching->isReached();