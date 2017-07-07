<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Tests;

use Kewodoa\Routing\Hierarchical\ConjunctionRoute;
use Kewodoa\Routing\Simple\SimpleMatching;
use Kewodoa\Routing\Simple\SimplePatternResolver;
use Kewodoa\Routing\Simple\SimpleRoute;
use Kewodoa\Routing\Simple\SimpleRouter;

class RelayTest extends \PHPUnit_Framework_TestCase{
	
	/** @var  ConjunctionRoute */
	protected $route;
	
	/** @var  SimpleRouter */
	protected $router;
	
	
	/**
	 *
	 */
	public function setUp(){
		// /users/{user.id}/notes/{note.id}
		$this->router = $router = new SimpleRouter(new SimplePatternResolver());
		
		$this->route = (new ConjunctionRoute('user:list','/users'))
			->setRouter($router)
			->addRoute(
				(new SimpleRoute('user:create','/create'))->setRouter($router)
			)
			->addRoute(
				(new ConjunctionRoute('user:view','/(?<uid>\d+)')) // matched destination on '/users/91'
				->setRouter($router)
					->addRoute( (new SimpleRoute('user:update', '/update'))->setRouter($router) )
					->addRoute( (new SimpleRoute('user:delete', '/delete'))->setRouter($router) )
					->addRoute(
						(new ConjunctionRoute('user:note:list','/notes'))
							->setRouter($router)
							->addRoute(
								(new ConjunctionRoute('user:note:view','/(?<note_id>\d+)'))
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
		
		
		// Placeholder worker
		
		
	}
	
	/**
	 * @expectedException
	 */
	public function testRoute(){
		$this->assertEquals($this->route->getRouter(), $this->router);
		$this->assertEquals($this->route->getDefaultReference(), 'user:update');
		$this->assertEquals($this->route->getPatternParams(), [ 'user_id' ]);
	}
	
	public function testRenderRoute(){
		// В SimplePatternResolver и SimpleRoute не проводится проверка сопоставления по входному шаблону, просто подстановка вместо плейсхолдера
		$this->assertEquals($this->route->render(['user_id' => 27]), '/users/27/update' );
		$this->assertEquals($this->route->render(['user_id' => '...']), '/users/.../update' );
		$this->assertEquals($this->route->render(['user_id' => null]), '/users//update' );
		$this->assertEquals($this->route->render([]), '/users//update' );
	}
	
	
	public function testMatching(){
		$matching = new SimpleMatching('/users/88/update');
		
		$this->assertEquals($matching->isConformed(), false);
		
		$matching = $this->router->match($matching);
		
		$this->assertEquals($matching->isConformed(), true);
		$this->assertEquals($matching->getReference(), 'user:update');
		$this->assertEquals($matching->getParams(), [
			'user_id' => '88' // Да да это строка, т.к в простом маршруте никаких приведений типов не проводится
		]);
	}
}


