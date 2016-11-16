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
	public $actions = [
		'taskdefined' => 'TaskDefined',
		'setting' => 'Setting',
		'rule' => 'Rule',
	];
	public $actions_values = [];
	public $values = [
		'taskdefined' => 'TaskDefined',
		'setting' => 'Setting',
		'rule' => 'Rule',
		'rulevalue' => 'Value',
		'ruleextra' => 'Extra',
		'ruledate' => 'Date'
	];
	public $values_values = [];
	public $weights = [];
	
	public function init() {
		// actions
		// translate
		foreach ($this->actions as $actions => $name){
			$this->actions[$actions] = Yii::t('app', $name);
		}
		
		// actions values
		$this->actions_values['taskdefined'] = TaskDefined::getAllIdName();
		$this->actions_values['setting'] = Setting::getAllIdName();
		$this->actions_values['rule'] = Rule::getAllIdName();
		$this->actions_values['rulevalue'] = RuleValue::getAllIdName();
		$this->actions_values['ruleextra'] = RuleExtra::getAllIdName();
		$this->actions_values['ruledate'] = RuleDate::getAllIdName();
		
		// values
		// translate
		foreach ($this->values as $values => $name){
			$this->values[$values] = Yii::t('app', $name);
		}
		
		// values_values
		$this->values_values['taskdefined'] = TaskDefined::getAllIdName();
		$this->values_values['setting'] = Setting::getAllIdName();
		$this->values_values['rule'] = Rule::getAllIdName();
		$this->values_values['rulevalue'] = RuleValue::getAllIdName();
		$this->values_values['ruleextra'] = RuleExtra::getAllIdName();
		$this->values_values['ruledate'] = RuleDate::getAllIdName();
		
		//$this->values = array_merge($modelRule->values, $modelRule->actions);
		//$this->values_values = $modelRule->values;
		
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
            [['action', 'action_value', 'value', 'value_value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['action', 'value'], 'string', 'max' => 128],
            [['action_value', 'value_value'], 'string', 'max' => 255]
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
            'action_value' => Yii::t('app', 'Action Value'),
            'value' => Yii::t('app', 'Value'),
            'value_value' => Yii::t('app', 'Value Value'),
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
