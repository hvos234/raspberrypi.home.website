<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\models\Setting;

/**
 * This is the model class for table "{{%rule_action}}".
 *
 * @property integer $id
 * @property string $action
 * @property string $value
 * @property integer $rule_id
 * @property integer $weight
 * @property string $created_at
 * @property string $updated_at
 */
class RuleAction extends \yii\db\ActiveRecord
{
	public $actions = [];
	public $weights = [];
	
	public function init() {
		// add all task defined
		$this->actions = array_merge($this->actions, TaskDefined::getAllEncoded());
		
		// add all setting
		$this->actions = array_merge($this->actions, Setting::getAllEncoded());
		
		// translate all actions
		foreach ($this->actions as $json => $action){
			$this->actions[$json] = Yii::t('app', $action);
		}
		
		// add date, condition or task before value condition
		foreach ($this->actions as $json => $action){
			$array = json_decode($json, true);
			
			switch($array['class']){
				case 'php':
					switch($array['function']){
						case 'date':
							$this->actions[$json] = sprintf('date(\'%s\'), %s', $array['parameter'], $action);
							break;
					}
					break;
				
				default:
					$this->actions[$json] = sprintf('%s, %s', $array['class'], $action);
			}
		}
				
		// create weights from 0 to 5
		for($weight = 0; $weight <= 4; $weight++){
			$this->weights[$weight] = $weight;
		}
		
		parent::init();
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rule_action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action', 'value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['action', 'value'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'action' => Yii::t('app', 'Action'),
            'value' => Yii::t('app', 'Value'),
            'rule_id' => Yii::t('app', 'Id Rule'),
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return RuleActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RuleActionQuery(get_called_class());
    }
		
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
}
