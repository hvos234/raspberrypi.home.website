<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cronjob */
/* @var $form yii\widgets\ActiveForm */

use dosamigos\datetimepicker\DateTimePicker;
?>

<div class="cronjob-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
	
    <?php //<?= $form->field($model, 'recurrence_minute')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'recurrence_minute')->dropDownList($model->recurrence_minutes); ?>

    <?php //<?= $form->field($model, 'recurrence_hour')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'recurrence_hour')->dropDownList($model->recurrence_hours); ?>

    <?php //<?= $form->field($model, 'recurrence_day')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'recurrence_day')->dropDownList($model->recurrence_days); ?>

    <?php //<?= $form->field($model, 'recurrence_week')->textInput() ?>
		<?= $form->field($model, 'recurrence_week')->dropDownList($model->recurrence_weeks); ?>

    <?php //<?= $form->field($model, 'recurrence_month')->textInput() ?>
		<?= $form->field($model, 'recurrence_month')->dropDownList($model->recurrence_months); ?>

    <?php //<?= $form->field($model, 'recurrence_year')->textInput() ?>
		<?= $form->field($model, 'recurrence_year')->dropDownList($model->recurrence_years); ?>

    <?php //<?= $form->field($model, 'job')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'job')->dropDownList($model->jobs); ?>

    <?php //<?= $form->field($model, 'job_id')->textInput() ?>
		<?= $form->field($model, 'task_id')->dropDownList($task_ids); ?>
	
		<?= $form->field($model, 'rule_id')->dropDownList($rule_ids); ?>

    <?php //<?= $form->field($model, 'start_at')->textInput() ?>
		<?= $form->field($model, 'start_at')->widget(DateTimePicker::className(), [
			'language' => 'nl',
			'size' => 'ms',
			'template' => '{input}',
			'pickButtonIcon' => 'glyphicon glyphicon-time',
			'inline' => false,
			'clientOptions' => [
					'startView' => 2,
					'minView' => 0,
					'maxView' => 4,
					'autoclose' => true,
					'format' => 'yyyy-mm-dd hh:ii:00',
			]
		]);?>
		
    <?php //<?= $form->field($model, 'end_at')->textInput() ?>
		<?= $form->field($model, 'end_at')->widget(DateTimePicker::className(), [
			'language' => 'nl',
			'size' => 'ms',
			'template' => '{input}',
			'pickButtonIcon' => 'glyphicon glyphicon-time',
			'inline' => false,
			'clientOptions' => [
					'startView' => 4,
					'minView' => 0,
					'maxView' => 4,
					'autoclose' => true,
					'format' => 'yyyy-mm-dd hh:ii:00',
			]
		]);?>

    <?php //<?= $form->field($model, 'run_at')->textInput() ?>

    <?php //<?= $form->field($model, 'created_at')->textInput() ?>

    <?php //<?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// this is the script that hide or show the job ids, when job is changed or selected.
$script = <<< JS
$(document).ready(function(){
	
	function setHideShowTaskRuleName(){
		// show all actions jopined to the device
		// get the value of to_device_id
		var job = $('#cronjob-job option:selected').val(); // returns null if nothing has selected
	
		if(null != job){
			// hide both task_id and rule_id
			$('.field-cronjob-task_id').hide();
			$('.field-cronjob-rule_id').hide();
			
			// select first job id
			$('#cronjob-' + job + '_id option:first-child').attr("selected", "selected");
			
			// show task or rule
			$('.field-cronjob-' + job + '_id').show();
	
			// enable job_id, as last if there is something wrong the
			// job_id will not be enabled
			$('#cronjob-' + job + '_id').removeAttr('disabled');
		}
	}
	
	// by default hide / show the actions
	setHideShowTaskRuleName();
	
	// if the to device change, hide or show the actions
	$('#cronjob-job').on('change', function() {
		setHideShowTaskRuleName();
	});
	
});
JS;

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END