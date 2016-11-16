<?php

namespace app\models;

use Yii;
use yii\base\Model;

// Models
use app\models\Rule;
use app\models\Condition;
use app\models\Setting;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class RuleValue extends Model {
	
	public static function models(){
		// the array key must be the same as the id
		return [ 
				'value' => (object) ['id' => 'value', 'name' => 'Value', 'value' => ''],
				1 => (object) ['id' => 1, 'name' => 'On', 'value' => 1],
				0 => (object) ['id' => 0, 'name' => 'Off', 'value' => 0],
			];
	}
	
	public static function all(){
			return RuleValue::models();
	}
	
	public static function one($id){
		$models = RuleValue::all();
		var_dump($id);
		var_dump($models);
		
		foreach($models as $model){
			echo('$model->id: ' . $model->id . ' == $id: ' . $id) . '<br/>' . PHP_EOL;
			if((string)$model->id == $id){
				return $model;
			}
		}
		return false;
	}
	
	public static function getAllIdName(){
		return ArrayHelper::map(RuleValue::all(), 'id', 'name');
	}
	
	public static function execute($id){
		$model = RuleValue::one($id);
		
		echo('RuleValue $id: ' . $id) . '<br/>' . PHP_EOL;
		echo('<pre>');
		print_r($model);
		echo('</pre>');
		//exit();
		
		if(!$model){
			return HelperData::dataExplode($id);
		}
		
		return HelperData::dataExplode($model->value);
	}
	
	public static function ruleCondition($id){
		return RuleValue::ruleExecute($id);
	}
	
	public static function ruleAction($id){
		return RuleValue::ruleExecute($id);
	}
	
	public static function ruleExecute($id){
		return RuleValue::execute($id);		
	}
}