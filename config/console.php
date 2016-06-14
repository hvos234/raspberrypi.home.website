<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
		'timezone' => 'Europe/Amsterdam',
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
						//'traceLevel' => YII_DEBUG ? 3 : 0,
						'traceLevel' => 0,
						'flushInterval' => 1, // log immediately
            'targets' => [
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										'exportInterval' => 1, // log immediately
										'categories' => ['cronjob'],
										'logFile' => '@app/runtime/logs/console/cronjob.log',
										'logVars' => [],
								],
                [
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										'exportInterval' => 1, // log immediately
										'categories' => ['task'],
										'logFile' => '@app/runtime/logs/console/task.log',
										'logVars' => [],
								],
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										'exportInterval' => 1, // log immediately
										'categories' => ['task-transmitter'],
										'logFile' => '@app/runtime/logs/console/task-transmitter.log',
										'logVars' => [],
								],
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										'exportInterval' => 1, // log immediately
										'categories' => ['task-receiver'],
										'logFile' => '@app/runtime/logs/console/task-receiver.log',
										'logVars' => [],
								],
								[
										'class' => 'yii\log\FileTarget',
										'levels' => ['trace', 'info', 'error', 'warning'],
										'exportInterval' => 1, // log immediately
										'categories' => ['rule'],
										'logFile' => '@app/runtime/logs/console/rule.log',
										'logVars' => [],
								],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
