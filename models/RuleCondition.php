<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\models\Setting;

/**
 * This is the model class for table "{{%rule_condition}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $condition
 * @property string $equation
 * @property string $value
 * @property integer $rule_id
 * @property integer $weight
 * @property string $created_at
 * @property string $updated_at
 */
class RuleCondition extends \yii\db\ActiveRecord
{
	public $conditions = [
		'taskdefined' => 'TaskDefined',
		'setting' => 'Setting',
		'rule' => 'Rule',
	];
	public $conditions_values = [];
	
	public $equations = [
		'==' => 'Equal',
		'!=' => 'Not equal',
		'>=' => 'Bigger or Equal', 
		'<=' => 'Smaller or Equal', 
		'>' => 'Bigger', 
		'<' => 'Smaller',
	];
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
		foreach ($this->conditions as $conditions => $name){
			$this->conditions[$conditions] = Yii::t('app', $name);
		}
		
		// actions values
		$this->conditions_values['taskdefined'] = TaskDefined::getAllIdName();
		$this->conditions_values['setting'] = Setting::getAllIdName();
		$this->conditions_values['rule'] = Rule::getAllIdName();
		$this->conditions_values['rulevalue'] = RuleValue::getAllIdName();
		$this->conditions_values['ruleextra'] = RuleExtra::getAllIdName();
		$this->conditions_values['ruledate'] = RuleDate::getAllIdName();
		
		// equations
		// translate all equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = Yii::t('app', $equation);
		}
		
		// key before value equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = $key . ', ' .  $equation;
		}
		
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
		
		// create weights from 0 to 10
		for($weight = 0; $weight <= 10; $weight++){
			$this->weights[$weight] = $weight;
		}
		
		parent::init();
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rule_condition}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['condition', 'condition_value', 'equation', 'value', 'value_value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['condition', 'value'], 'string', 'max' => 128],
            [['condition_value', 'value_value'], 'string', 'max' => 255],
            [['equation'], 'string', 'max' => 4]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'condition' => Yii::t('app', 'Condition'),
            'condition_value' => Yii::t('app', 'Condition Value'),
            'equation' => Yii::t('app', 'Equation'),
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
     * @return RuleConditionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RuleConditionQuery(get_called_class());
    }
		
		public static function execute($rule_id){
			$models = RuleCondition::findAll(['rule_id' => $rule_id]);
			foreach($models as $model){
				
			}
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
