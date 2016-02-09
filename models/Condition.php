<?php

namespace app\models;

use Yii;
use yii\base\Model;

// Models
use app\models\Condition;
use app\models\Setting;

class Condition extends Model {
	
	public static function models(){
		// the array key must be the same as the id
		return [ 
				1 => (object) ['id' => 1, 'name' => 'I am really at home', 'function' => 'IamReallyAthome'],
			];
	}
	
	public static function all(){
			return Condition::models();
	}
	
	public static function one($id){
		$models = Condition::all();
		foreach($models as $model){
			if($model->id == $id){
				return $model;
			}
		}
		return false;
	}
	
	public static function execute($id){
		$model = Condition::one($id);
		return call_user_func('app\models\Condition::' . $model->function); // use app\models\ or else it cannot find class
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
		
		return $iamathome;
	}
}