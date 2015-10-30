<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RuleCondition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rule-condition-form">

    <?php $form = ActiveForm::begin(); ?>

		<?php
		foreach($models as $index => $model){
		?>
			<?= $form->field($model, "[$index]name")->textInput(['maxlength' => true])->label($model->name) ?>

			<?= $form->field($model, "[$index]condition")->textInput(['maxlength' => true])->label($model->condition) ?>

			<?= $form->field($model, "[$index]equation")->textInput(['maxlength' => true])->label($model->equation) ?>

			<?= $form->field($model, "[$index]value")->textInput(['maxlength' => true])->label($model->value) ?>

			<?php //<?= $form->field($model, "[$index]rule_id")->textInput()->label($model->rule_id) ?>

			<?= $form->field($model, "[$index]weight")->textInput()->label($model->weight) ?>

			<?php //<?= $form->field($model, "[$index]created_at")->textInput()->label($model->created_at) ?>

			<?php //<?= $form->field($model, "[$index]updated_at")->textInput()->label($model->updated_at) ?>
		<?php
		}
		?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
