<?php

return [
	'resources' => [
		'myshares' => ['url' => '/shares'],
		'myshares_api' => ['url' => '/api/0.1/shares']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'myshares_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
	]
];
