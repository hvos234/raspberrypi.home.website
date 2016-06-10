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
	public $conditions = [];
	public $conditions_values = [];
	
	public $equations = [
		'==' => 'Equal',
		'!=' => 'Not equal',
		'>=' => 'Bigger or Equal', 
		'<=' => 'Smaller or Equal', 
		'>' => 'Bigger', 
		'<' => 'Smaller',
	];
	public $weights = [];
	
	public function init() {
		// get all conditions
		$modelRule = new Rule();
		$this->conditions = $modelRule->conditions_actions;
		
		// get all conditions values
		$this->conditions_values = $modelRule->values;
		
		
		// translate all equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = Yii::t('app', $equation);
		}
		
		// key before value equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = $key . ', ' .  $equation;
		}
		
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
            [['condition', 'condition_value', 'equation', 'value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['condition', 'value'], 'string', 'max' => 128],
            [['condition_value'], 'string', 'max' => 255],
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
