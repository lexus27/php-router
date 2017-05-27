<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing;

use Kewodoa\Routing\Exception\SkipException;
use Kewodoa\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RouteAbstract
 * @package Kewodoa\Routing
 */
abstract class RouteAbstract implements Route{


	/** @var  Router */
	protected $router;

	/** @var string */
	protected $pattern;

	/** @var  null|array */
	protected $pattern_options;

	/** @var array  */
	protected $options = [];

	/** @var mixed */
	protected $reference;

	/** @var array  */
	protected $params = [];

	/**
	 * SimpleRouteAbstract constructor.
	 * @param $pattern
	 * @param $pattern_options
	 * @param $reference
	 */
	public function __construct($reference, $pattern, $pattern_options = null){
		$this->pattern      = $pattern;
		$this->reference    = $reference;
	}

	/**
	 * @param Router $router
	 * @return $this
	 */
	public function setRouter(Router $router){
		if($this->router !== $router){
			$this->router = $router;
		}
		return $this;
	}

	/**
	 * @return Router
	 * @throws RoutingException
	 */
	public function getRouter(){
		if(!$this->router){
			throw new RoutingException('Router not be setup!');
		}
		return $this->router;
	}


	/**
	 * @param Matching $matching
	 * @return Matching
	 * @throws SkipException
	 */
	public function match(Matching $matching){

		$this->_doMatch($matching);

		if($matching->isReached()){
			$this->_checkEnv($matching);
			$this->_matchingConformed($matching);
		}else{
			$matching->setConformed(false);
		}

		return $matching;
	}

	protected function _doMatch(Matching $matching){
		$path = $matching->getProposedPath();
		if( false !== $params = $this->_matchPath($path, $received)){
			$matching->setConformed(true);
			$matching->setConformedPath($received);
			$matching->setRoute($this);
			$matching->setParams($params);
		}
		return $matching;
	}

	public function render($params){
		$resolver = $this->getRouter()->getPatternResolver();
		$params = $this->_prepareRenderParams($params);
		return $resolver->patternRender($params, $this->pattern, $this->pattern_options);
	}

	/**
	 * @return array
	 * @throws RoutingException
	 */
	public function getPatternParams(){
		$pattern_resolver = $this->getRouter()->getPatternResolver();
		return $pattern_resolver->patternPlaceholders($this->pattern, $this->pattern_options);
	}

	/**
	 * @return mixed
	 */
	public function getDefaultReference(){
		return $this->reference;
	}

	/**
	 * @return array
	 */
	public function getDefaultParams(){
		return $this->params;
	}

	/**
	 * @param $path
	 * @param $matched_received
	 * @return array|false
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

	/**
	 * @param Matching $matching
	 */
	protected function _matchingConformed(Matching $matching){
		$matching->setReference($this->reference);
		$matching->setParams((array)$this->params, true);
	}

	protected function _checkEnv(Matching $matching){

	}

	protected function _prepareRenderParams($params){
		return $params;
	}


}


