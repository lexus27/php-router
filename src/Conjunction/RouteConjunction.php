<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Conjunction;
use Kewodoa\Routing\Matching;
use Kewodoa\Routing\Route;
use Kewodoa\Routing\RouteAbstract;
use Kewodoa\Routing\RoutingException;


/**
 * Связка
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteConjunction
 * @package Kewodoa\Routing
 */
class RouteConjunction extends RouteAbstract{

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

		if(!$matching instanceof DeeperMatchingInterface){
			// error not valid instance Matching must be implements DeeperMatching
			throw new RoutingException('Expected implements '.DeeperMatchingInterface::class.', passed a "'.get_class($matching).'"');
		}

		parent::match($matching);
		// если наш маршрут совпадает
		if($matching->isConformed()){
			$matching->deepIn();
			try{
				if($matching->getPath()){
					$matching->setConformed(false); // conformed OFF before
					foreach($this->routes as $route){
						$m = $route->match($matching);
						if($m->isConformed()){
							// conformed ON
							break;
						}
					}
				}elseif($this->is_symbolic){
					$matching->setConformed(false);
				}
			}finally{
				$matching->deepOut();
			}
		}
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

}

