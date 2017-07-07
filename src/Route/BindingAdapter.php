<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Route;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class BindingAdapter
 * @package Kewodoa\Routing\Route
 */
class BindingAdapter{
	
	/**
	 * @param $field_path
	 * @param $field_value
	 * @param $binding_rule
	 * @param array $pattern_params
	 * @param null $full_path
	 * @return null
	 */
	public function fetch($field_path, $field_value, $binding_rule, $pattern_params = [], $full_path = null){
		// можно это инкапсулировать в объекты Binding;, объект будет обращаться к Router за адаптером Orm(selectById)
		if(is_string($binding_rule)){
			$classname = $binding_rule;
			$criteria = null;
		}elseif(is_array($binding_rule)){
			if(isset($binding_rule['class'])){
				$classname = $binding_rule['class'];
			}
			if(isset($binding_rule['criteria'])){
				$criteria = $binding_rule['criteria'];
			}
		}
		
		// полный путь
		// полный разделенный путь
		// путь до поля
		// разделенный путь до поля
		
		
		$object = new \stdClass();
		$object->{$field_path} = $field_value;
		return $object;
	}
	
}


