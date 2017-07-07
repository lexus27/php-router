<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

namespace Kewodoa\Routing;
use Kewodoa\Routing\Route\BindingAdapter;
use Kewodoa\Routing\Route\PatternResolver;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Router
 * @package Kewo\Matching
 *
 * Каждый Маршрут - это пирс для клиента
 *
 * Важно понимать, что роутер способен на:
 * Восприятие структуры разделения приложений
 * Вытаскивать данные из запроса на вход в контроллер
 * Формировать ссылку на некий контроллер
 *
 *
 *
 *
 *
 * Роутер подготавливает и вытаскивает из внешнего запроса input для передачи его в диспетчеризацию
 *
 * @todo Способ разбора шаблонов
 * @todo Компиляция шаблонов и нативных проверок
 * @todo Делегирование input для MCA системы
 * @todo Предоставление информации для генерации URL и ссылок на диспетчеризации
 * @todo
 */
interface Router extends Matchable{

	/**
	 * @return PatternResolver
	 */
	public function getPatternResolver();
	
	/**
	 * @todo Необходимо поведение при отсутствии адаптера
	 * @return BindingAdapter
	 */
	public function getBindingAdapter();
	
	/**
	 * @param Matching $routing
	 * @return \Generator|Matching[]
	 */
	public function matchLoop(Matching $routing);

	/**
	 * @param Matching $matching
	 * @return Matching
	 */
	public function match(Matching $matching);

}


