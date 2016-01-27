<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RuleCondition */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Rule Condition',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rule Conditions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="rule-condition-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
