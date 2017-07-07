<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */
namespace Kewodoa\Routing;
use Kewodoa\Routing\Conjunction\DeeperMatching;
use Kewodoa\Routing\Conjunction\RouteConjunction;
use Kewodoa\Routing\Hierarchical\ConjunctionFactory;
use Kewodoa\Routing\Simple\SimpleMatching;
use Kewodoa\Routing\Simple\SimplePatternResolver;
use Kewodoa\Routing\Simple\SimpleRoute;
use Kewodoa\Routing\Simple\SimpleRouteFactory;
use Kewodoa\Routing\Simple\SimpleRouter;

include '../vendor/autoload.php';

class Conjunction{}

interface OpenedScope{

	/**
	 * @return OpenedScope
	 */
	public function getParent();

	/**
	 * @return Route
	 */
	public function getRoute();

	/**
	 * @return bool
	 */
	public function isSymbolic();

}

interface ConfigScope{

	public function getPatternResolver();

	public function getReferenceResolver();

}
interface StackRoutes{

	public function getRoute($identifier);

	public function addRoute(Route $route, $identifier = null);

	public function indexOf(Route $route);

	public function match(Matching $matching);

}
interface RouteAware{

	public function getRoute();

}












$resolver   = new SimplePatternResolver();
$router     = new SimpleRouter($resolver);

$route1     = new SimpleRoute('user:follow', '/users/(?<user_id>[1-9][0-9]+)/follow');
$route2     = new SimpleRoute('user:create', '/users/(?<user_id>[1-9][0-9]+)/follow');
$route3     = new SimpleRoute('user:follow', '/users/(?<user_id>[1-9][0-9]+)/follow');
$route4     = new SimpleRoute('user:follow', '/users/(?<user_id>[1-9][0-9]+)/follow');

$router->addRoute($route1);
$router->addRoute($route2);
$router->addRoute($route3);
$router->addRoute($route4);


$matching   = new SimpleMatching('/users/91/follow');
$matching = $router->match($matching);

$params = [
	'user_id' => 12
];
$rendered_path = $route1->render($params);


// Маршрут должен помещаться в какой-то стек для определения подходящего.
// Маршруту нужен какой-то контекст настройки, это мог бы быть и Маршрутизатор(Стек)

// Маршрут должен знать свой контекст настройки
// Стек маршрутов должен знать маршруты

/**
 *
 * Информация после маршрутизации:
 *      Ссылка на действие.
 *      Параметры для действия.
 *      Параметры для контекста.
 *      Узел в иерархии разделения, Родительский узел: получение родительского объекта.
 */
echo htmlspecialchars($rendered_path);







$примерноеОпределениеМассивами = [
	//'name' => null, Имена определяются в регистре а не частно
	'type'      => RouteConjunction::class,
	'action'    => 'user:list',
	'pattern'   => '/users',
	//'symbolic'  => true, // or 'phantom' => true,
	'children'  => [[
		'action'    => 'user:create',
		'pattern'   => '/create',
	],[
		'type'      => RouteConjunction::class,
		'reference' => 'user:view',
		'pattern'   => '/(?<uid>\d+)',
		'children'  => [[
			'action'    => 'user:delete',
			'pattern'   => '/delete',
		],[
			'action'    => 'user:update',
			'pattern'   => '/update',
		]],
	]]
];

$conjunction = (new RouteConjunction('user:list','/users'))
	->setRouter($router)
	->addRoute(
		(new SimpleRoute('user:create','/create'))->setRouter($router)
	)
	->addRoute(
		(new RouteConjunction('user:view','/(?<uid>\d+)')) // matched destination on '/users/91'
			->setRouter($router)
			->addRoute( (new SimpleRoute('user:update', '/update'))->setRouter($router) )
			->addRoute( (new SimpleRoute('user:delete', '/delete'))->setRouter($router) )
	);


// scope пока нету, пока без getParent()

// todo: Обработка matchedConformed checkEnv. Разделение абстракции от реализации
// Есть такой прикол, ConjunctionRoute и SimpleRoute разделены и никак не расширяют друг друга, тоесть при кастомизации
// областных обработчиков мы не можем расчитывать на наследование, ни у одного класса ни у другого,
// не реализована работа с \Closure например, тоесть с кастомными обработчиками matchedConformed и checkEnv
// В пределах одной области может действовать уникальный PatternResolver к которому мы получаем доступ через Скоп
// Резольвер работает с шаблоном и его параметрами, зафиксированными в маршруте
// У маршрута так-же могут быть опции, и мы возможно можем их читать через спец объекты которым по паттерну Visitor
// будет доступен использующий его в данный момент Маршрут гость
// Можно ввести плагины в "Скоп", которые будут дотягиваться до частного поведения маршрута и определять свое поведение,
// но базируясь на конфигах Самого Маршрута, считывание конфигов будет инкапсулированно в плагине, а определение конфига
// к маршруту будет обрамленно в красивый интерфейс-определения такого типа маршрута
$matching = new DeeperMatching('/users/91');
$matching = $conjunction->match($matching);








$p = '/users/91/notes/87/update';
$p1 = '/users/91/ba';
$matching = new SimpleMatching($p1);
$result = $conjunction->match($matching);
// ConjunctionRoute(Связка) ~~~ Relay(Реле)


/**
 *
 * Стек это реле
 * маршруты не знают про родителя
 * но при матчинге реле, мы имеем декоратор
 *
 * Связь с родительским маршрутом со стороны дочернего, для получения его аттрибутов
 *
 * захват параметров из родительской цепочки
 * требование параметров из родительской цепочки и общий процессинг их перед отрисовкой ссылки
 * соединение шаблонов к конечному маршруту
 * соблюдение нужных очередей прохода по линейному стеку маршрутов
 *
 *
 * события
 * Сопоставление шаблона conformed
 * Проверка окружения
 * Достижение полного совпадение fulfilled Или Reached
 *
 */
























$director = new FactoryDirector($router);

$director->setFactory(new ConjunctionFactory(),Conjunction::class);
$director->setFactory(new SimpleRouteFactory(),SimpleRoute::class);
$director->setDefault(SimpleRoute::class);


$route = $director->createRoute([
	'type'      => Conjunction::class,
	'action'    => 'user:list',
	'pattern'   => '/users',
	//'symbolic'  => true, // or 'phantom' => true,
	'children'  => [[
		'action'    => 'user:create',
		'pattern'   => '/create',
	],[
		'type'      => Conjunction::class,
		'reference' => 'user:view',
		'pattern'   => '/(?<uid>\d+)',
		'children'  => [[
			'action'    => 'user:delete',
			'pattern'   => '/delete',
		],[
			'action'    => 'user:update',
			'pattern'   => '/update',
		]],
	]]
]);

$d = 1;

