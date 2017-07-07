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
			$p = isset($definition['pattern'])?$definition['pattern']:null;
			$po = isset($definition['pattern_options'])?$definition['pattern_options']:[];
			$a = isset($definition['action'])?$definition['action']:null;
			$route = new SimpleRoute($a, $p, $po);
			$route->setRouter($director->getRouter());
			$route->setOptions(array_diff_key($definition,array_flip(['pattern','pattern_options','action'])));
			return $route;
		}
		return null;
	}

}


