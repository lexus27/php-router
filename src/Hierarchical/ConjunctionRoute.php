<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Hierarchical;

use Kewodoa\Routing\Exception\Matching\SkipException;
use Kewodoa\Routing\Matching;
use Kewodoa\Routing\Route;
use Kewodoa\Routing\RouteAbstract;
use Kewodoa\Routing\RoutingException;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ConjunctionRoute
 * @package Kewodoa\Routing\Hierarchical
 *
 * kwd: Channel(Канал), Relay(Реле), ConjunctionRoute(Связка)
 */
class ConjunctionRoute extends RouteAbstract{

	/** @var  Route[]  */
	protected $routes = [];

	/**
	 * Означает что он не может быть Reached, и на него не может быть ссылки.
	 * Делегирует обработку дочерним маршрутам при сопоставлении себя.
	 * @var bool
	 */
	protected $is_symbolic = false;
	
	/**
	 * @param Route $route
	 * @return $this
	 */
	public function addRoute(Route $route){
		$this->routes[] = $route;
		return $this;
	}
	
	/**
	 * @return Route[]
	 */
	public function getRoutes(){
		return $this->routes;
	}
	
	/**
	 * @param callable $callback
	 * @return array
	 */
	public function findWayTo(callable $callback){
		$to_high = [];
		$this->_find_way($callback,$to_high);
		return $to_high;
	}
	
	public function _find_way(callable $callback, &$way = [], $a = []){
		$a[] = $this;
		foreach($this->routes as $c){
			if(call_user_func($callback, $c, $a)){
				$way[] = $c;
				$way[] = $this;
				return true;
			}else if($c instanceof ConjunctionRoute && $c->_find_way($callback, $way, $a)){
				$way[] = $this;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param Matching $matching
	 * @return Matching
	 * @throws RoutingException
	 */
	public function match(Matching $matching){

		$this->_doMatch($matching);

		if(!$matching->isConformed()){
			$matching->reset();
			return $matching;
		}else{
			$this->_matchingConformed($matching);
		}

		$isReached = $matching->isReached();
		if(!$this->is_symbolic && ($isReached || !$this->routes)){
			$this->_matchingReached($matching);
			return $matching;
		}
		if($this->is_symbolic && ($isReached || !$this->routes)){
			return $matching->setConformed(false);
		}

		$result = null;
		$decorator = $this->decorateMatching($matching);

		foreach($this->routes as $route){
			try{
				$result = $route->match($decorator);
				if($result->isConformed()){
					
					break;
				}
				$result = null;
			}catch (SkipException $e){}
		}
		
		if($decorator->isReached() || ($result instanceof MatchingDecorator && $result->isReached())){
			$result->apply();// Вызывается для декоратора
			$this->_matchingReached($result);
			return $result?$result:$decorator;
		}
		
		$matching->setConformed(false);
		$matching->reset();
		return $matching;
		
	}
	
	/**
	 * @param $path
	 * @param null $matched_received
	 * @return array|bool|false
	 * @throws RoutingException
	 */
	protected function _matchPath($path, &$matched_received = null){
		$resolver = $this->getRouter()->getPatternResolver();
		$data = $resolver->patternMatchStart($path, $this->pattern, $this->pattern_options, $matched_received);
		if($data === false){
			$matched_received = null;
		}
		return $data;
	}

	protected function _matchingConformed(Matching $matching){
		parent::_matchingConformed($matching);
	}
	
	
	/**
	 * @param Matching $matching
	 */
	protected function _checkEnv(Matching $matching){
		
	}
	
	/**
	 * @param Matching $matching
	 */
	protected function _matchingReached(Matching $matching){

	}

	/**
	 * @param $matching
	 * @return MatchingDecorator
	 */
	protected function decorateMatching($matching){
		return new MatchingDecorator($matching);
	}
	
}


