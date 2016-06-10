<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

/*$settingModel = Setting::find()->where(['name' => 'date_default_timezone'])->one();
if(isset($settingModel->data) and !empty($settingModel->data)){
	date_default_timezone_set($settingModel->data);
}*/

(new yii\web\Application($config))->run();
