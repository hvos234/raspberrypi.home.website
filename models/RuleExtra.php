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

class RuleExtra extends Model {
	
	public static function models(){
		// the array key must be the same as the id
		return [ 
				1 => (object) ['id' => 1, 'name' => 'I am really at home', 'function' => 'IamReallyAthome'],
			];
	}
	
	public static function all(){
			return RuleExtra::models();
	}
	
	public static function one($id){
		$models = RuleExtra::all();
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
		return ArrayHelper::map(RuleExtra::all(), 'id', 'name');
	}
	
	public static function execute($id){
		echo('RuleExtra $id: ' . $id) . '<br/>' . PHP_EOL;
		$model = RuleExtra::one($id);
		echo('<pre>');
		print_r($model);
		echo('</pre>');
		//exit();
		
		return call_user_func('app\models\RuleExtra::' . $model->function); // use app\models\ or else it cannot find class
	}
	
	public static function ruleCondition($id){
		return RuleExtra::ruleExecute($id);
	}
	
	public static function ruleAction($id){
		return RuleExtra::ruleExecute($id);
	}

	public static function ruleExecute($id){
		return RuleExtra::execute($id);		
	}
	
	public static function IamReallyAthome(){
		$ip_addressen = Setting::find()->select('data')->where(['name' => 'i_am_really_at_home_ip_addressen'])->one();
		$ip_addressen = HelperData::dataExplode($ip_addressen->data);
		
		$iamathome = false;
		foreach ($ip_addressen as $ip_adres){
			$command = 'sudo ping  ' . $ip_adres . ' -c 2'; // -c 2 (two time on linux machine
			exec(escapeshellcmd($command), $output, $return_var);
			
			if(0 == $return_var){
				$iamathome = true;
			}
		}
		
		return HelperData::dataExplode($iamathome);
	}
}