<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Container;


class Container{
	
	/**
	 * @param $path
	 * @return mixed
	 */
	public function getParam($path){
		return $path;
	}
	
	public function hasParam($path){
		return false;
	}
	
	public function setParam($path, $value){
		
	}
	
	public function removeParam($path, $value){
		
	}
	
}


