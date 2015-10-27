<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cronjob */

$this->title = Yii::t('app', 'Create Cronjob');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cronjobs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cronjob-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'task_ids' => $task_ids,
        'rule_ids' => $rule_ids,
    ]) ?>

</div>
