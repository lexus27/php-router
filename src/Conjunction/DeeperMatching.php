<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Conjunction;

use Kewodoa\Routing\MatchingAbstract;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class DeeperMatching
 * @package Kewodoa\Routing\Conjunction
 */
class DeeperMatching extends MatchingAbstract implements DeeperMatchingInterface{

	protected $depth = 0;

	protected $path;

	protected $paths_chunks = [];

	public function __construct($path){
		$this->path = $path;
	}

	public function deepIn(){
		$this->depth++;
		if($this->path){
			$ahead_path = substr($this->path, strlen($this->conformed_path));
			$this->paths_chunks[] = $this->conformed_path;
			$this->path = $ahead_path;
			$this->conformed_path = null;
		}else{
			// в таком случае $this->depth++; является пустышкой и не нужно нам вообще, ведет только к логическим проблемам
		}
	}

	public function deepOut(){
		$this->depth--;
		if($this->depth < 0){
			$this->depth = 0;
		}

		if($this->paths_chunks){
			$behind_path = array_pop($this->paths_chunks);
			$this->path = $behind_path . $this->path;
			$this->conformed_path = $behind_path . $this->conformed_path; // Собираем все назад чтобы получить полный путь
		}else{
			// вызов deepOut здесь уже как следствие логических проблем
		}
	}

	/**
	 *
	 * Полезно вызывать перед deepIn для проверки
	 *
	 * @return bool
	 */
	public function hasAhead(){
		return !!substr($this->path, strlen($this->conformed_path));
	}

	/**
	 * @return int
	 */
	public function getDepth(){
		return $this->depth;
	}

	/**
	 * @return string
	 */
	public function getPath(){
		return $this->path;
	}
}