<?php
return [
    'id' => 'service',
	'name' => 'Services',
    'basePath' => __DIR__,
	'timeZone' => 'America/El_Salvador',
    'defaultRoute' => 'site/index',
    'aliases' => [],
	'bootstrap' => ['log'],
	'components' => [
        'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;port=3306;dbname=apitest',
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		],
        'user' => [
            'identityClass' => 'app\models\Usuario',
            'enableAutoLogin' => true,
            'enableSession'=>false,
            'loginUrl' => null,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
			'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [ 
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => true,
                    'controller' => 'test',
                    'extraPatterns' => [
                        'GET,OPTIONS <id:\d+>' => 'view',
                        'POST,OPTIONS search' => 'search',
                        'POST,OPTIONS create' => 'create',
                        'PUT,OPTIONS update' => 'update',
                        'POST,OPTIONS delete' => 'delete',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => true,
                    'controller' => 'catalog',
                    'extraPatterns' => [
                        'GET,OPTIONS <id:\d+>' => 'view',
                        'GET,OPTIONS list/<cat_codigo_padre>' => 'list',
                        'POST,OPTIONS search' => 'search',
                        'POST,OPTIONS create' => 'create',
                        'PUT,OPTIONS update' => 'update',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'pluralize' => true,
                    'controller' => 'user',
                    'extraPatterns' => [
                        'GET,OPTIONS <id:\d+>' => 'view',
                        'POST,OPTIONS search' => 'search',
                        'POST,OPTIONS create' => 'create',
                        'PUT,OPTIONS update' => 'update',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'pluralize' => false,
                    'controller' => 'security',
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'POST,OPTIONS logout' => 'logout',
                    ],
                ],
            ],
        ],
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'class' => '\yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                
                // re format response data if:
                // - httpstatus code is an error 
                // - we have a response format equals to FORMAT_JSON
                if ($response->statusCode !== 200 || $response->format == yii\web\Response::FORMAT_JSON)
                {
                    $response_data = [];
                    $http_data = [
                        'httpSuccess' => $response->isSuccessful,
                        'httpStatus'  => $response->statusCode,
                        'httpMessage' => $response->statusText,
                    ];

                    // if the http status code is equals to "200", add to response the service request response
                    if ($response->statusCode === 200) {
                        $response_data = array("httpResponse" => $response->data);
                    }

                    $response->format = yii\web\Response::FORMAT_JSON;
                    $response->data = array_merge($http_data, $response_data);
                }
            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/error.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logFile' => '@runtime/logs/warning.log',
                    'logVars' => ['_GET','_POST'],
                ],
            ],
        ],
    ],
    'params' => [
        'authKeyTimeLife' => 300,
    ],
];