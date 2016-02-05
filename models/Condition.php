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
		//var_dump($model);
		//// object(stdClass)#68 (3) { ["id"]=> int(1) ["name"]=> string(19) "I am really at home" ["function"]=> string(15) "IamReallyAthome" } 
		//exit();
		return call_user_func('app\models\Condition::' . $model->function);
		//var_dump($return);
		//return 'Condition, id: ' . $model->id . ' name: ' . $model->name . ' function: ' . $model->function . ' return: ' . var_export($return);
	}

	public static function IamReallyAthome(){
		//$ip_addressen = Setting::getOneByName('i_am_really_at_home_ip_addressen');
		$ip_addressen = Setting::find()->select('data')->where(['name' => 'i_am_really_at_home_ip_addressen'])->one();
		//var_dump($ip_addressen->data);
		//exit();
		$ip_addressen = HelperData::dataExplode($ip_addressen->data);
		var_dump($ip_addressen);
		//exit();
		$iamathome = false;
		foreach ($ip_addressen as $ip_adres){
			$command = 'sudo ping  ' . $ip_adres . ' -c 2'; // -c 2 (two time on linux machine
			var_dump(escapeshellcmd($command));
			//exit();
			
			exec(escapeshellcmd($command), $output, $return_var);

			// try again
			if(0 != $return_var){
				exec(escapeshellcmd($command), $output, $return_var);
				if(0 != $return_var){
					return false;
				}
			}
			$iamathome = true;
		}
		var_dump($iamathome);
		return $iamathome;
	}
}