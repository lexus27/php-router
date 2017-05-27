<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing;


use Kewodoa\Routing\Nested\NestedRouteFactory;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class FactoryDirector
 * @package Kewodoa\Routing
 */
class FactoryDirector{

	/** @var  Router */
	protected $router;

	/** @var FactoryMethodInterface[]  */
	protected $methods = [];
	protected $method_default;

	public function __construct(Router $router){
		$this->router = $router;
	}

	public function setFactory(FactoryMethodInterface $factory, $name = null){
		$this->methods[is_null($name)?get_class($factory):$name] = $factory;
	}

	/**
	 * @param array $definition
	 * @return Route
	 * @throws RoutingException
	 */
	public function createRoute(array $definition){
		$factory = null;
		if(isset($definition['type'])){
			if(isset($this->methods[$definition['type']])){
				$factory = $this->methods[$definition['type']];
			}else{
				throw new RoutingException('RouteFactoryMethod not found by name "'.$definition['type'].'"');
			}
			unset($definition['type']);
		}else{
			if($this->method_default instanceof FactoryMethodInterface){
				$factory = $this->method_default;
			}elseif(isset($this->methods[$this->method_default])){
				$factory = $this->methods[$this->method_default];
			}
		}
		if(!$factory) return null;

		return $factory->create($definition, $this);
	}

	public function getRouter(){
		return $this->router;
	}

	public function setDefault($class){
		$this->method_default = $class;
		return $this;
	}
}


