<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Hierarchical;


use Kewodoa\Routing\FactoryDirector;
use Kewodoa\Routing\FactoryMethodInterface;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ConjunctionFactory
 * @package Kewodoa\Routing\Hierarchical
 */
class ConjunctionFactory implements FactoryMethodInterface{

	/**
	 * @param array $definition
	 * @param FactoryDirector $context
	 * @return ConjunctionRoute
	 */
	public function create($definition, FactoryDirector $context){
		
		$p = isset($definition['pattern'])?$definition['pattern']:null;
		$po = isset($definition['pattern_options'])?$definition['pattern_options']:[];
		$a = isset($definition['action'])?$definition['action']:null;
		
		$relay = new ConjunctionRoute($a, $p, $po);
		$relay->setRouter($context->getRouter());
		$relay->setOptions(array_diff_key($definition,array_flip(['pattern','pattern_options','action','children'])));
		if($definition['children']){
			foreach($definition['children'] as $child){
				$relay->addRoute($context->createRoute($child));
			}
		}
		return $relay;
	}

}


