<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Simple;

use Kewodoa\Routing\FactoryDirector;
use Kewodoa\Routing\FactoryMethodInterface;
use Kewodoa\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Factory
 * @package Kewodoa\Routing\Simple
 */
class SimpleRouteFactory implements FactoryMethodInterface{
	
	/**
	 * @param $definition
	 * @param FactoryDirector $director
	 * @return Route
	 */
	public function create($definition, FactoryDirector $director){

		if(is_array($definition)){
			$route = new SimpleRoute($definition['action'], $definition['pattern'], $definition['pattern_options']);
			$route->setRouter($director->getRouter());
			return $route;
		}
		return null;
	}

}


