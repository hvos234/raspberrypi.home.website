<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

use app\models\Cronjob;
use app\models\CronjobSearch;

use app\models\TaskDefined;
use app\models\TaskDefinedSearch;

/**
 * If cron execute it the default date and time are wrong,
 * this fix it
 */
date_default_timezone_set('Europe/Amsterdam');

/**
 * This console controller is called by the server cron
 */
class CronController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        $modelCronjob = new Cronjob();
				$modelCronjob->cron();
    }
}
