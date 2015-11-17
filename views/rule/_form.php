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
				<th><?= Yii::t('app', 'Condition'); ?></th>
				<th><?= Yii::t('app', 'Equation'); ?></th>
				<th><?= Yii::t('app', 'Value'); ?></th>
				<th><?= Yii::t('app', 'Weight'); ?></th>
			</tr>
			<?php
			foreach($modelsRuleCondition as $index => $modelRuleCondition){
				$modelRuleCondition['weight'] = (empty($modelRuleCondition['weight']) ? $index : $modelRuleCondition['weight']);
				?>
				<tr id="RuleCondition_<?= $index; ?>" class="RuleCondition-row" style="display:<?= (Yii::t('app', '- None -') == $modelRuleCondition['value'] ? 'none' : 'table-row') ?>;">
					<td><?= $form->field($modelRuleCondition, "[$index]condition")->dropDownList($modelRuleCondition->conditions)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]equation")->dropDownList($modelRuleCondition->equations)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]value", ['inputOptions' => ['class' => 'form-control RuleCondition-value']])->textInput(['maxlength' => true])->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]weight")->dropDownList($modelRuleCondition->weights)->label(false) ?></td>
				</tr>
				<?php
			}
			?>
		</table>
		
		<p>
			<?= Html::button(Yii::t('app', 'Add Condition'), ['id' => 'RuleCondition_add', 'style' => 'display:none;']) ?>
			<?= Html::button(Yii::t('app', 'Remove Condition'), ['id' => 'RuleCondition_remove', 'style' => 'display:none;']) ?>
		</p>
		
		<h2><?= Yii::t('app', 'Actions'); ?></h2>
		<table>
			<tr>
				<th><?= Yii::t('app', 'Action'); ?></th>
				<th><?= Yii::t('app', 'Value'); ?></th>
				<th><?= Yii::t('app', 'Weight'); ?></th>
			</tr>
			<?php
			foreach($modelsRuleAction as $index => $modelRuleAction){
				$modelRuleAction['weight'] = (empty($modelRuleAction['weight']) ? $index : $modelRuleAction['weight']);
				?>
				<tr id="RuleAction<?= $index; ?>" class="RuleAction-row" style="display:<?= (Yii::t('app', '- None -') == $modelRuleAction['value'] ? 'none' : 'table-row') ?>;">
					<td><?= $form->field($modelRuleAction, "[$index]action")->dropDownList($modelRuleAction->actions)->label(false) ?></td>
					<td>
						<table>
							<tr>								
								<td><?= $form->field($modelRuleAction, "[$index]values", ['inputOptions' => ['class' => 'form-control RuleAction-values', 'index' => $index]])->dropDownList($modelRuleAction->values)->label(false) ?></td>
								<td><?= $form->field($modelRuleAction, "[$index]value", ['inputOptions' => ['class' => 'form-control RuleAction-value', 'index' => $index]])->textInput(['maxlength' => true, 'readonly' => array_key_exists($modelRuleAction['value'], $modelRuleAction->values)])->label(false) ?></td>
							</tr>
						</table>
					</td>
					<td><?= $form->field($modelRuleAction, "[$index]weight")->dropDownList($modelRuleAction->weights)->label(false) ?></td>
				</tr>
				<?php
			}
			?>
		</table>
		
		<p>
			<?= Html::button(Yii::t('app', 'Add Action'), ['id' => 'RuleAction_add', 'style' => 'display:none;']) ?>
			<?= Html::button(Yii::t('app', 'Remove Action'), ['id' => 'RuleAction_remove', 'style' => 'display:none;']) ?>
		</p>
		
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// this is the script that hide or show the action, when the 
// to device is changed or select.
$none = Yii::t('app', '- None -');

$script = <<< JS
$(document).ready(function(){
    // on click Add Condition button (RuleCondition_add)
    // loop trough all the table rows and set by the
    // first one the display on table-row and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleCondition_add').on('click', function() {
        $('.RuleCondition-row').each(function( index ) {
            if('none' == $(this).css('display')){
                $(this).find('.RuleCondition-value').val('');
                $(this).css('display', 'table-row');
                RuleConditionShowHideButton();
                return false;
            }
        });
        RuleConditionShowHideButton();
        return false;
    });
    
    // on click Remove Condition button (RuleCondition_remove)
    // loop trough all the table rows and set by the
    // last one the display on none and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleCondition_remove').on('click', function() {
        // first in reverse order
        $($('.RuleCondition-row').get().reverse()).each(function(index) { 
            if('table-row' == $(this).css('display')){
                $(this).find('.RuleCondition-value').val('{$none}');
                $(this).css('display', 'none');
                RuleConditionShowHideButton();
                return false;
            }
        });
        RuleConditionShowHideButton();
        return false;
    });
    
    // loop trough all the RuleCondition-row, and
    // count how many are visible (display table-row)
    // and show or hide the Add Condition button (RuleCondition_add) 
    // or the Remove Condition button (RuleCondition_remove)
    function RuleConditionShowHideButton(){
        var count = 0;
        var visible = 0;
        $('.RuleCondition-row').each(function( index ) {
            count++;
            if('table-row' == $(this).css('display')){
                visible++;
            }
        });
        if(1 >= visible){
            $('#RuleCondition_add').css('display', 'inline-block');
            $('#RuleCondition_remove').css('display', 'none');
        }
        if(1 < visible){
            $('#RuleCondition_add').css('display', 'inline-block');
            $('#RuleCondition_remove').css('display', 'inline-block');
        }
        if(count <= visible){
            $('#RuleCondition_add').css('display', 'none');
            $('#RuleCondition_remove').css('display', 'inline-block');
        }
    }
    
    RuleConditionShowHideButton();
    
    // on click Add Condition button (RuleCondition_add)
    // loop trough all the table rows and set by the
    // first one the display on table-row and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleAction_add').on('click', function() {
        $('.RuleAction-row').each(function( index ) {
            if('none' == $(this).css('display')){
                $(this).find('.RuleAction-value').val('');
                $(this).css('display', 'table-row');
                RuleActionShowHideButton();
                return false;
            }
        });
        RuleActionShowHideButton();
        return false;
    });
    
    // on click Remove Condition button (RuleCondition_remove)
    // loop trough all the table rows and set by the
    // last one the display on none and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleAction_remove').on('click', function() {
        // first in reverse order
        $($('.RuleAction-row').get().reverse()).each(function(index) { 
            if('table-row' == $(this).css('display')){
                $(this).find('.RuleAction-value').val('{$none}');
                $(this).css('display', 'none');
                RuleActionShowHideButton();
                return false;
            }
        });
        RuleActionShowHideButton();
        return false;
    });
    
    // loop trough all the RuleCondition-row, and
    // count how many are visible (display table-row)
    // and show or hide the Add Condition button (RuleCondition_add) 
    // or the Remove Condition button (RuleCondition_remove)
    function RuleActionShowHideButton(){
        var count = 0;
        var visible = 0;
        $('.RuleAction-row').each(function( index ) {
            count++;
            if('table-row' == $(this).css('display')){
                visible++;
            }
        });
        if(1 >= visible){
            $('#RuleAction_add').css('display', 'inline-block');
            $('#RuleAction_remove').css('display', 'none');
        }
        if(1 < visible){
            $('#RuleAction_add').css('display', 'inline-block');
            $('#RuleAction_remove').css('display', 'inline-block');
        }
        if(count <= visible){
            $('#RuleAction_add').css('display', 'none');
            $('#RuleAction_remove').css('display', 'inline-block');
        }
    }
    
    RuleActionShowHideButton();
    
    // change the value with the value of the values, if 
    // changed
    $('.RuleAction-values').each(function() {
        $(this).on('change', function() {
            var index = $(this).attr('index');
            if('value' == $(this).val()){
                $("input[name='RuleAction["+index+"][value]']").removeAttr('readonly');
                $("input[name='RuleAction["+index+"][value]']").val('');
            }else {
                $("input[name='RuleAction["+index+"][value]']").attr('readonly', 'readonly');
                $("input[name='RuleAction["+index+"][value]']").val($(this).val());
            }
        });
    });
    
    // set the selected value from values
    $('.RuleAction-values').each(function() {
        var index = $(this).attr('index');
        $(this).val($("input[name='RuleAction["+index+"][value]']").val());        
    });
});
JS;

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END