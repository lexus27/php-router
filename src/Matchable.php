<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface Matchable
 * @package Kewodoa\Routing
 */
interface Matchable{

	/**
	 * @param Matching $matching
	 * @return mixed
	 */
	public function match(Matching $matching);

}

