<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Nested;

use Kewodoa\Routing\Exception\SkipException;
use Kewodoa\Routing\RouteAbstract;
use Kewodoa\Routing\Matching;
use Kewodoa\Routing\Route;
use Kewodoa\Routing\RoutingException;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteRelay
 * @package Kewodoa\Routing\Nested
 *
 * kwd: Channel(Канал), Relay(Реле), Conjunction(Связка)
 */
class RouteRelay extends RouteAbstract{

	/** @var   */
	protected $opened_scope;

	/** @var  Route[]  */
	protected $routes = [];

	/** @var bool  */
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
			$result->apply();
			$this->_matchingReached($result);
			return $result?$result:$decorator;
		}
		
		$matching->setConformed(false);
		$matching->reset();
		return $matching;
		
		
		if($decorator->isReached()){
			$decorator->apply();
			$this->_matchingReached($result);
			return $result?$result:$decorator;
		}else{
			$matching->setConformed(false);
			$matching->reset();
			return $matching;
		}

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

	protected function _checkEnv(Matching $matching){

	}


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


