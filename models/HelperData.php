<?php

namespace app\models;

use Yii;
use yii\base\Model;

class HelperData extends Model {
	
	/**
	 * Explode the string by the : and , delimiter
	 * 
	 * @param type $string (string to explode)
	 * @return type
	 */
	public static function dataExplode($string){
		$return = [];
		$keys = HelperData::dataExplodeKey($string);
		foreach($keys as $key => $value){
			if(is_array($keyvalue = HelperData::dataExplodeKeyValue($value))){
				$return = array_merge($return, $keyvalue);
			}else {
				$return[] = $keyvalue;
			}
		}
		
		return $return;
	}
	
	public static function dataExplodeKey($string){
		$return = [];
		$array = explode(',', $string);
		foreach($array as $key => $value){
			$return[] = trim($value);
		}
		
		return $return;
	}
	
	public static function dataExplodeKeyValue($string){
		$return = [];
		if(false !== strpos($string, ':')){
			$strpos = strpos($string, ':');
			$before = substr($string, 0, $strpos);
			$after = substr($string, $strpos+1, strlen($string));
			$return[trim($before)] = HelperData::dataExplodeKeyValue($after);
		}else {
			$return = trim($string);
		}
		
		return $return;
	}
	
	/**
	 * Implode the array by : and , delimiter
	 * 
	 * @param type $array (array to implode)
	 * @return type
	 */
	public static function dataImplode($array){
		$return = HelperData::dataImplodeKey($array);		
		return $return;
	}
	
	public static function dataImplodeKey($array){
		$return = '';
		foreach($array as $key => $value){
			if(!is_int($key)){
				if(!is_array($value)){
					$return .= $key . ':'  . $value . ',';
				}else {
					$return .= $key . ':' . HelperData::dataImplodeKeyValue($value) . ',';
				}
			
			}else {
				if(!is_array($value)){
					$return .= $value . ',';
				}else {
					$return .= $key . ':' . HelperData::dataImplodeKeyValue($value) . ',';
				}
			}
		}
		
		return substr($return, 0, -1);
	}
	
	public static function dataImplodeKeyValue($array){
		$return = '';
		foreach($array as $key => $value){
			if(!is_array($value)){
				$return .= $key . ':' . $value;
			}else {
				$return .= $key . ':' . HelperData::dataImplodeKeyValue($value);
			}
		}
		
		return $return;
	}
	
	/**
	 * Trims all the data
	 * 
	 * @param type $data (string to trim)
	 * @return type
	 */
	public static function dataTrim($data){
		$data = trim($data);
		$array = HelperData::dataExplode($data);
		return HelperData::dataImplode($array);
	}
	
	/**
	 * Replace all the , for a return
	 * 
	 * @param type $data (string to replace)
	 * @return type
	 */
	public static function dataExplodeReturn($data){
		return str_replace(',', "\r\n", $data);
	}
	
	/**
	 * Replace all the return with a ,
	 * 
	 * @param type $data (string to replace)
	 * @return type
	 */
	public static function dataImplodeReturn($data){
		return str_replace(array("\r\n", "\r", "\n"), ',', $data);
	}
}