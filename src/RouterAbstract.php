<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */
namespace Kewodoa\Routing;

use Kewodoa\Routing\Exception\Matching\SkipException;
use Kewodoa\Routing\Route\BindingAdapter;
use Kewodoa\Routing\Route\PatternResolver;
use Kewodoa\Routing\Simple\SimpleMatching;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouterAbstract
 * @package Kewodoa\Routing
 */
abstract class RouterAbstract implements Router{

	/** @var  Route[]  */
	protected $routes = [];

	/** @var  PatternResolver */
	protected $pattern_resolver;

	/** @var  BindingAdapter */
	protected $binding_adapter;

	/**
	 * RouterAbstract constructor.
	 * @param PatternResolver $resolver
	 */
	public function __construct(PatternResolver $resolver){
		$this->pattern_resolver = $resolver;
	}

	/**
	 * @return PatternResolver
	 */
	public function getPatternResolver(){
		return $this->pattern_resolver;
	}


	/**
	 * @param Matching $matching
	 * @return \Generator|Matching[]
	 */
	public function matchLoop(Matching $matching){
		foreach($this->routes as $route){
			try{
				$match = $route->match($matching);
				if($match->isConformed()){
					yield $match;
				}
			}catch (SkipException $skipping){
				$matching->reset();
			}
		}
	}

	/**
	 * @param Matching $matching
	 * @return Matching
	 */
	public function match(Matching $matching){
		foreach($this->routes as $route){
			try{
				$route->match($matching);
				if($matching->isConformed()){
					return $matching;
				}else{
					$matching->reset();
				}
			}catch (SkipException $skipping){
				$matching->reset();
			}
		}
		return $matching;
	}

	/**
	 * @param \Kewodoa\Routing\Route $route
	 * @param null $name
	 * @return $this
	 */
	public function addRoute(Route $route, $name=null){
		$route->setRouter($this);
		if($name===null){
			$this->routes[] = $route;
		}else{
			$this->routes[$name] = $route;
		}
		return $this;
	}
	
	public function getRoutes(){
		return array_values($this->routes);
	}
	
	/**
	 * ИСП. ЕСЛИ: Единица Вычисления создается в Маршрутизаторе
	 * @param $path
	 * @return SimpleMatching
	 */
	public function factoryMatching($path){
		return new SimpleMatching($path);
	}
	
	/**
	 * @param BindingAdapter $adapter
	 * @return $this
	 */
	public function setBindingAdapter(BindingAdapter $adapter){
		$this->binding_adapter = $adapter;
		return $this;
	}
	
	public function getBindingAdapter(){
		return $this->binding_adapter;
	}

}