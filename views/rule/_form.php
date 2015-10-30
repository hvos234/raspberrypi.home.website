<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Rule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
	
		<?= $form->field($model, 'weight')->dropDownList($model->weights) ?>

    <?php //<?= $form->field($model, 'created_at')->textInput() ?>

    <?php //<?= $form->field($model, 'updated_at')->textInput() ?>
		
		<h2><?= Yii::t('app', 'Conditions'); ?></h2>
		<table>
			<tr>
				<th><?= Yii::t('app', 'Name'); ?></th>
				<th><?= Yii::t('app', 'Condition'); ?></th>
				<th><?= Yii::t('app', 'Equation'); ?></th>
				<th><?= Yii::t('app', 'Value'); ?></th>
				<th><?= Yii::t('app', 'Weight'); ?></th>
				<th>&nbsp;</th>
			</tr>
			<?php
			foreach($modelsRuleCondition as $index => $modelRuleCondition){
				//var_dump($modelRuleCondition);
				//$modelRuleCondition->[$index]['weight'] = (empty($modelRuleCondition->[$index]['weight']) ? $key : $modelRuleCondition->[$index]['weight']);
				?>
				<tr>
					<td><?= $form->field($modelRuleCondition, "[$index]name")->textInput(['maxlength' => true])->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]condition")->dropDownList($modelRuleCondition->conditions)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]equation")->dropDownList($modelRuleCondition->equations)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]value")->textInput(['maxlength' => true])->label(false) ?></td>
					<?php //<?= $form->field($modelRuleCondition, "[$index]rule_id")->textInput()->label($modelRuleCondition->rule_id) ?>
					<td><?= $form->field($modelRuleCondition, "[$index]weight")->dropDownList($modelRuleCondition->weights)->label(false) ?></td>
					<?php //<?= $form->field($modelRuleCondition, "[$index]created_at")->textInput()->label($modelRuleCondition->created_at) ?>
					<?php //<?= $form->field($modelRuleCondition, "[$index]updated_at")->textInput()->label($modelRuleCondition->updated_at) ?>
					<td><?= Html::submitButton('Remove', ['name' => "RuleCondition_remove[$index]", 'value' => $index]) ?></td>
				<tr>
				<?php
			}
			?>
		</table>
		
		<p>
			<?= Html::submitButton('Add Condition', ['name' => 'RuleCondition_add']) ?>
			
		</p>
		
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
