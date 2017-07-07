<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing;

use Kewodoa\Routing\Exception\Matching\MissingException;
use Kewodoa\Routing\Exception\Matching\SkipException;
use Kewodoa\Routing\Exception\Matching\StabilizeException;

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

	/** @var mixed */
	protected $reference;

	/** @var array  */
	protected $params = [];
	
	/** @var array  */
	protected $options = [];
	
	/**
	 * SimpleRouteAbstract constructor.
	 * @param $pattern
	 * @param $pattern_options
	 * @param $reference
	 */
	public function __construct($reference, $pattern, $pattern_options = null){
		$this->pattern          = $pattern;
		$this->pattern_options  = $pattern_options;
		$this->reference        = $reference;
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
	
	public function setOptions(array $options){
		$this->options = $options;
		return $this;
	}
	public function getOptions(){
		return $this->options;
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
	 * @throws \Kewodoa\Routing\Exception\Matching\SkipException
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
	
	/**
	 * @param $params
	 * @return string
	 */
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
		$b = [
			'classname' => 'UserClass',
			'from' => 'user_id',
			'to' => 'id',
		];
		
		
		$matching->setReference($this->reference);
		
		
		
		$pattern_params = $params = array_replace((array)$this->params, $matching->getParams());
		
		$bindings = isset($this->options['objects'])?$this->options['objects']:[];
		
		$delimiter = $this->getRouter()->getPatternResolver()->getPathDelimiter();
		
		if($delimiter){
			foreach($this->getPatternParams() as $param_key_def){
				$chunks = $this->_decomposite_path($param_key_def, $delimiter);
				$container_key = $chunks[0];
				if(isset($bindings[$container_key])){
					$binding_rule = $bindings[$container_key];
					$object_id = $params[$param_key_def];
					$query_key = $this->_composite_path($chunks, $delimiter);
					
					$object = $this->_fetch_binding(
						$query_key,$object_id, $binding_rule, $pattern_params, $param_key_def
					);
					$params[ $container_key ] = $this->_checkoutBoundedObject(
						$object, $pattern_params, $container_key, $param_key_def
					);
					unset($params[$param_key_def]);
				}
			}
		}
		
		$matching->setParams($params, true);
	}
	
	protected function _decomposite_path($param, $path_delimiter){
		return explode($path_delimiter,$param);
	}
	
	protected function _composite_path($chunks, $path_delimiter){
		array_shift($chunks);
		return implode($path_delimiter, $chunks);
	}
	
	/**
	 * Выдача связанного объекта (Проверка и т.п)
	 * @param object $object
	 * @param array $pattern_params
	 * @param $container_key
	 * @param $param_key_def
	 * @return object
	 * @throws MissingException|SkipException
	 */
	protected function _checkoutBoundedObject($object,array $pattern_params, $container_key, $param_key_def){
		if(!$object){
			if(isset($this->options['static']) && $this->options['static']){
				throw new MissingException();
			}else{
				throw new SkipException();
			}
		}
		return $object;
	}
	
	/**
	 * Обрабатывает метаданные связывания, и берет нужный объект из бд
	 * @param $field_path
	 * @param $field_value
	 * @param $binding_rule
	 * @param array $pattern_params
	 * @param null $full_path
	 * @return null
	 */
	protected function _fetch_binding($field_path, $field_value, $binding_rule, $pattern_params = [], $full_path = null){
		return $this->getRouter()->getBindingAdapter()->fetch($field_path, $field_value, $binding_rule, $pattern_params, $full_path);
	}
	
	/**
	 * Проверка окружения: выброс Skip в случае неудачи.
	 *
	 * @param Matching $matching
	 */
	protected function _checkEnv(Matching $matching){
		
	}
	
	/**
	 * Подготовка массива параметров
	 * @param $params
	 * @return mixed
	 */
	protected function _prepareRenderParams($params){
		return $params;
	}


}


