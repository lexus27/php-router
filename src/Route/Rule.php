<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Route;
use Kewodoa\Routing\Matching;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Rule
 * @package Kewodoa\Routing\Route
 */
class Rule{
	
	const MODE_STRICT       = 'strict';
	const MODE_STABILIZED   = 'stabilized';
	
	public $path;
	
	public $expected;
	
	public $strict = false;
	
	public $stabilized = false;
	
	
	public function match(Matching $matching){
		$value = $this->getFromMatching($matching, $this->path);
		if(!$this->check( $value , $this->expected)){
			if($this->strict){
				$matching->unexpectedRequest([
					$this->path => [
						'invalid_value'  => $value,
						'expected_value' => $this->expected,
						
					]
				]);
			}elseif($this->stabilized){
				$matching->stabilizeRequestWith([
					$this->path => $this->expected
				]);
			}else{
				$matching->skip();
			}
		}
	}
	
	public function getFromMatching(Matching $matching, $path){
		return $matching->getEnv($path);
	}
	
	public function check($a, $b){
		return $a === $b;
	}
	
	public function setMode($mode){
		$this->strict = $mode===self::MODE_STRICT;
		$this->stabilized = $mode===self::MODE_STABILIZED;
		return $this;
	}
	
	
	
}


