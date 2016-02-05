<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%setting}}".
 *
 * @property string $name
 * @property string $description
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 */
class Setting extends \yii\db\ActiveRecord
{		
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'data'], 'required'],
            [['description', 'data'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
						[['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return SettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingQuery(get_called_class());
    }
		
		/*public static function primaryKey()
		{	
			return ['name'];
		}*/
		
		/**
		 * Auto add date time to created_at and updated_at
		 */
		public function behaviors()
		{
			return [
					// This set the create_at and updated_at by create, and 
					// update_at by update, with the date time / timestamp
					[
						'class' => TimestampBehavior::className(),
						'createdAtAttribute' => 'created_at',
						'updatedAtAttribute' => 'updated_at',
						'value' => new Expression('NOW()'),
					],
			 ];
		}
		
		public static function encodeName($name){
			$name = strtolower($name);
			$name = str_replace(' ', '_', $name);
			$name = str_replace('-', '_', $name);
			do {
				$done = strpos($name, '__');
				$name = str_replace('__', '_', $name);
			} while ($done);
			
			return $name;
		}
		
		public static function decodeName($name){
			$name = ucfirst($name);
			$name = str_replace('_', ' ', $name);
			
			return $name;
		}
		
		public function createOne($paramters){
			$model = new Setting();
			
			foreach($paramters as $field => $value){
				$model->{$field} = $value;
			}
			
			return $model->save();
		}
		
		public function changeOne($id, $parameters){
			$model = Setting::findOne($id);
			
			foreach($paramters as $field => $value){
				$model->{$field} = $value;
			}
			
			return $model->save();
		}


		public static function getAll(){
			// get all the task defined
			return Setting::find()->asArray()->all();
		}
		
		public static function getAllIdName(){
			return ArrayHelper::map(Setting::find()->asArray()->all(), 'id', 'name');
		}
		
		public static function getAllByIdAndName(){
			return ArrayHelper::map(Setting::find()->asArray()->all(), 'name', 'description');
		}
		
		public static function getAllEncoded(){
			$return = [];
			
			$settings = Setting::getAll();
			foreach($settings as $setting){
				$array = ['class' => 'Setting', 'function' => 'changeOne', 'id' => $setting['name']];
				$return[HelperData::dataImplode($array)] = sprintf('(%s) %s', $setting['name'], substr($setting['description'], 0, 100));
				//$return[sprintf('{"class":"Setting","function":"changeOne","id":"%s"}', $setting['name'])] = sprintf('(%s) %s', $setting['name'], substr($setting['description'], 0, 100));
			}
			
			return $return;
		}
		
		/*public static function getOneByName($name){
			return Setting::find()->select('data')->where(['name' => $name])->asArray()->one();
		}*/
}