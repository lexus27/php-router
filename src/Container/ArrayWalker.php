<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Container;


class ArrayWalker{
	
	protected $container;
	
	protected $array = [];
	
	protected $array_result = [];
	
	/**
	 * ArrayWalker constructor.
	 * @param array $array
	 * @param Container $container
	 */
	public function __construct(array $array, Container $container){
		$this->array = $array;
		$this->container = $container;
		$this->array_result = $this->check($array);
	}
	
	/**
	 * @param array $array
	 * @return array
	 */
	public function check(array $array){
		foreach($array as $key => $value){
			$_key = $this->_fetchValue($key,$changed);
			if($changed) unset($array[$key]);
			
			if(is_array($value)){
				$_value = $this->check($value);
			}else{
				$_value = $this->_fetchValue($value);
			}
			
			$array[$_key] = $_value;
		}
		return $array;
	}
	
	/**
	 * @param $value
	 * @param bool $changed
	 * @return mixed
	 */
	public function _fetchValue($value, &$changed = false){
		if(is_string($value)){
			if(substr($value,0,1)==='{' && substr($value,-1)==='}'){
				$path = substr($value,1,-1);
				if($this->container->hasParam($path)){
					$value = $this->container->getParam($path);
				}else{
					$value = null;
				}
				$changed = true;
			}
		}
		return $value;
		
	}
	
	public function toArray(){
		
	}
	
	
}


