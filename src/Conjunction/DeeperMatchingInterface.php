<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing\Conjunction;


/**
 * Вычисление с функционалом контроля глубины
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface DeeperMatching
 * @package Kewodoa\Routing
 */
interface DeeperMatchingInterface{

	public function deepIn();

	public function deepOut();

	public function getDepth();

}
