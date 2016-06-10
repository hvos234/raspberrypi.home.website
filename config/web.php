<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
		'language' => 'nl', // added to support i18n, netherlands
		'timezone' => 'Europe/Amsterdam',
		/*'formatter' => [
			'defaultTimeZone' => 'UTC',
			'timeZone' => 'Europe/Amsterdam',
		],*/
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'fga1234FAS34nlf83nbLHYfs8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
						'traceLevel' => 0,
						//'flushInterval' => 1000, // log immediately
            'targets' => [
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										//'exportInterval' => 1, // log immediately
										'categories' => ['task'],
										'logFile' => '@app/runtime/logs/task.log',
										'logVars' => [],
								],
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										//'exportInterval' => 1, // log immediately
										'categories' => ['task-transmitter'],
										'logFile' => '@app/runtime/logs/task-transmitter.log',
										'logVars' => [],
								],
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										//'exportInterval' => 1, // log immediately
										'categories' => ['task-receiver'],
										'logFile' => '@app/runtime/logs/task-receiver.log',
										'logVars' => [],
								],
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										//'exportInterval' => 1, // log immediately
										'categories' => ['rule'],
										'logFile' => '@app/runtime/logs/rule.log',
										'logVars' => [],
								],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
				'allowedIPs' => ['127.0.0.1', '::1', '192.168.192.*'] // adjust this to your needs
    ];
}

return $config;
