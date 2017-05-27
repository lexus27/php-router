<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Nested;


use Kewodoa\Routing\FactoryDirector;
use Kewodoa\Routing\FactoryMethodInterface;

class NestedRouteFactory implements FactoryMethodInterface{

	/**
	 * @param array $definition
	 * @param FactoryDirector $context
	 * @return RouteRelay
	 */
	public function create($definition, FactoryDirector $context){
		$relay = new RouteRelay(
			$definition['action'],
			$definition['pattern'],
			$definition['pattern_options']
		);
		$relay->setRouter($context->getRouter());
		if($definition['children']){
			foreach($definition['children'] as $child){
				$relay->addRoute($context->createRoute($child));
			}
		}
		return $relay;
	}

}


