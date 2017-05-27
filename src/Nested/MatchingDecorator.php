<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Nested;

use Kewodoa\Routing\Matching;
use Kewodoa\Routing\MatchingAbstract;
use Kewodoa\Routing\Route;

/**
 *
 * Декоратор, может помоч при ситуации когда нам не нужно требовать DeeperMatchingInterface в глобальном вычислении
 * Чтобы не ограничивать при наличии Релешных маршрутов
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MatchingDecorator
 * @package Kewodoa\Routing\Conjunction
 * todo Пересмотреть RouteConjunction для такого использования matching`а
 */
class MatchingDecorator extends MatchingAbstract{

	/** @var Matching|MatchingDecorator  */
	protected $wrapped;

	protected $depth = 0;

	public function __construct(Matching $matching){
		$this->wrapped          = $matching;
		$this->conformed_path   = null;
		$this->proposed_path    = substr(
			$matching->getProposedPath(),
			strlen($matching->getConformedPath())
		);


		$depth = $matching instanceof MatchingDecorator? $matching->getDepth() : 0;
		$this->depth = $depth + 1;
	}

	/**
	 * @return int
	 */
	public function getDepth(){
		return $this->depth;
	}

	/**
	 * В случае FULFILLED, Применение параметров
	 */
	public function apply(){
		//$behind = $this->wrapped->getConformedPath();
		//$this->wrapped->setConformedPath($behind . $this->conformed_path);
		//$this->wrapped->setProposedPath($behind . $this->proposed_path);

		//$this->wrapped->setParams($this->params, true);
		//$this->wrapped->setReference($this->reference);
		//$this->wrapped->setRoute($this->route);

	}



	public function getPath(){
		return $this->wrapped->getPath();
	}

	/**
	 * @return Matching
	 */
	public function getRootWrapped(){
		if($this->wrapped instanceof MatchingDecorator){
			return $this->wrapped->getRootWrapped();
		}else{
			return $this->wrapped;
		}
	}

	/**
	 * @return Matching|MatchingDecorator
	 */
	public function getWrapped(){
		return $this->wrapped;
	}

}


