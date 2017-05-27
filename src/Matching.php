<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing;

use Kewodoa\Routing\Exception\SkipException;


/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Matching
 * @package Kewo\Matching
 */
interface Matching{

	/**
	 * @return string
	 */
	public function getPath();

	/**
	 * @param $path
	 * @return $this
	 */
	public function setProposedPath($path);

	/**
	 * @return string
	 */
	public function getProposedPath();

	/**
	 * @return mixed
	 * Ссылка на действие
	 */
	public function getReference();

	/**
	 * @return array
	 */
	public function getParams();

	/**
	 * @param Route $route
	 * @return $this
	 */
	public function setRoute(Route $route);

	/**
	 * @param $reference
	 * @return mixed
	 */
	public function setReference($reference);

	/**
	 * @param array $params
	 * @param bool $merge
	 * @return mixed
	 */
	public function setParams(array $params, $merge = true);

	/**
	 * @param bool|true $conformed
	 * @return $this
	 */
	public function setConformed($conformed = true);

	/**
	 * @return boolean
	 */
	public function isConformed();

	/**
	 * @return boolean
	 */
	public function isReached();

	/**
	 * @param $received
	 * @return $this
	 */
	public function setConformedPath($received);

	/**
	 * @return mixed
	 */
	public function getConformedPath();

	/**
	 * @throws SkipException
	 */
	public function skip();

	/**
	 * @return void
	 */
	public function reset();
	
	
	public function setOption($key, $value);

	public function getOption($key);

	public function setOptions(array $options, $merge = true);

	public function getOptions();

}


