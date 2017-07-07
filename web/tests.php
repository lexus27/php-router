<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-router
 */

$route = [
	'pattern' => 'http://kewodoa.ru',
	'action' => 'index:index:index',
	'location' => [
		'title'     => 'Добро пожаловать',
		'pagetitle' => '{site_name}',
		'menutitle' => 'Главная',
	],
	'children' => [[
		'pattern' => '/blog',
		'action' => 'index:blog:list',
		'location' => [
			'title' => 'Блог',
			'pagetitle' => 'Блог / {&parent}',
			'menutitle' => 'Блог',
		],
		'children' => [[
			'pattern'   => '/{article.id}',
			'action'    => 'index:blog:read',
			'objects'   => [
				'article' => 'Article/Class',
			],
		],[
			'pattern' => '/management',
			'symbolic' => true,
			'children' => [[
				'pattern' => '/new',
				'action' => 'index:blog:create',
				'location' => [
					'title' => 'Создание статьи',
					'pagetitle' => 'Создать статью / {&parent}',
					'menutitle' => 'Создать статью',
				]
			],[
				'pattern' => '/{article.id}',
				'action'    => 'index:blog:update',
				'objects' => [
					'article' => 'Article/Class',
				],
				'children' => [[
					'pattern' => '/delete',
					'action'    => 'index:blog:delete',
					
					'location' => [
						'success' => [
							'redirect' => '&parent.parent.parent'
						]
					]
					
				]]
			]]
		]]
		
		
	]],
	
];
