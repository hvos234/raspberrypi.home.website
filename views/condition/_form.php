<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Condition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="condition-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php //<?= $form->field($model, 'condition')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'condition')->dropDownList($model->conditions); ?>

    <?php //<?= $form->field($model, 'equation')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'equation')->dropDownList($model->equations); ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?php //<?= $form->field($model, 'created_at')->textInput() ?>

    <?php //<?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
