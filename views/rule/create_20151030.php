<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Rule */

$this->title = Yii::t('app', 'Create Rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelRuleCondition' => $modelRuleCondition,
    ]) ?>

</div>
