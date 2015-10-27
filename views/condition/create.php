<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Condition */

$this->title = Yii::t('app', 'Create Condition');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Conditions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="condition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
