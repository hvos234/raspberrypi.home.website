<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use yii\helpers\ArrayHelper;

use app\models\RuleCondition;
use app\models\RuleAction;

/**
 * This is the model class for table "{{%rule}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class Rule extends \yii\db\ActiveRecord
{
	/*public $functions = [
		// date
		'class:php,function:date,parameter:d' => 'Day of the month, 2 digits with leading zeros (01 to 31)',
		//'date(\'j\')' => 'Day of the month without leading zeros (1 to 31)',
		'class:php,function:date,parameter:D' => 'A textual representation of a day, three letters (Mon through Sun)',
		'class:php,function:date,parameter:l' => 'A full textual representation of the day of the week (Sunday through Saturday)',
		'class:php,function:date,parameter:N' => 'ISO-8601 numeric representation of the day of the week (1 (for Monday) through 7 (for Sunday))',
		//'date(\'w\')' => 'Numeric representation of the day of the week (0 (for Sunday) through 6 (for Saturday))',
		//'date(\'z\')' => 'The day of the year (starting from 0 through 365)',
		'class:php,function:date,parameter:W' => 'ISO-8601 week number of year, weeks starting on Monday (42 is the 42nd week in the year)',
		'class:php,function:date,parameter:m' => 'Numeric representation of a month, with leading zeros (01 through 12)',
		//'date(\'n\')' => 'Numeric representation of a month, without leading zeros (1 through 12)',
		'class:php,function:date,parameter:F' => 'A full textual representation of a month (January through December)',
		'class:php,function:date,parameter:M' => 'A short textual representation of a month, three letters (Jan through Dec)',
		///'date(\'t\')' => 'Number of days in the given month (28 through 31)',
		'class:php,function:date,parameter:Y' => 'A full numeric representation of a year, 4 digits (1999 or 2003)',
		//'date(\'y\')' => 'A two digit representation of a year (99 or 03)',
		///'date(\'L\')' => 'Whether it's a leap year (1 if it is a leap year, 0 otherwise)',
		///'date(\'o\')' => 'ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead (1999 or 2003)',
		///'date(\'a\')' => 'Lowercase Ante meridiem and Post meridiem (am or pm)',
		///'date(\'A\')' => 'Uppercase Ante meridiem and Post meridiem (AM or PM)',
		///'date(\'B\')' => 'Swatch Internet time (000 through 999)',
		//'date(\'g\')' => '12-hour format of an hour without leading zeros (1 through 12)',
		//'date(\'G\')' => '24-hour format of an hour without leading zeros (0 through 23)',
		//'date(\'h\')' => '12-hour format of an hour with leading zeros (01 through 12)',
		'class:php,function:date,parameter:H' => '24-hour format of an hour with leading zeros (00 through 23)',
		'class:php,function:date,parameter:i' => 'Minutes with leading zeros (00 to 59)',
		'class:php,function:date,parameter:s' => 'Seconds, with leading zeros (00 through 59)',
		///'date(\'u\')' => 'Microseconds (654321)',
		'class:Condition,function:amiathome,parameter:\'\'' => 'Am i at Home',
		///'Task::execute($from_device_id,$to_device_id,$action_id)' => 'Task, {id} {name}',
		
	];*/
	
	public $conditions_actions = [
		'date' => 'Date',
		//'task' => 'Task',
		'taskdefined' => 'TaskDefined',
		'setting' => 'Setting',
	];
	
	
	public $values = [
		'date' => [
			// date
			'd' => 'Day of the month, 2 digits with leading zeros (01 to 31)',
			//'date(\'j\')' => 'Day of the month without leading zeros (1 to 31)',
			'D' => 'A textual representation of a day, three letters (Mon through Sun)',
			'l' => 'A full textual representation of the day of the week (Sunday through Saturday)',
			'N' => 'ISO-8601 numeric representation of the day of the week (1 (for Monday) through 7 (for Sunday))',
			//'date(\'w\')' => 'Numeric representation of the day of the week (0 (for Sunday) through 6 (for Saturday))',
			//'date(\'z\')' => 'The day of the year (starting from 0 through 365)',
			'W' => 'ISO-8601 week number of year, weeks starting on Monday (42 is the 42nd week in the year)',
			'm' => 'Numeric representation of a month, with leading zeros (01 through 12)',
			//'date(\'n\')' => 'Numeric representation of a month, without leading zeros (1 through 12)',
			'F' => 'A full textual representation of a month (January through December)',
			'M' => 'A short textual representation of a month, three letters (Jan through Dec)',
			///'date(\'t\')' => 'Number of days in the given month (28 through 31)',
			'Y' => 'A full numeric representation of a year, 4 digits (1999 or 2003)',
			//'date(\'y\')' => 'A two digit representation of a year (99 or 03)',
			///'date(\'L\')' => 'Whether it's a leap year (1 if it is a leap year, 0 otherwise)',
			///'date(\'o\')' => 'ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead (1999 or 2003)',
			///'date(\'a\')' => 'Lowercase Ante meridiem and Post meridiem (am or pm)',
			///'date(\'A\')' => 'Uppercase Ante meridiem and Post meridiem (AM or PM)',
			///'date(\'B\')' => 'Swatch Internet time (000 through 999)',
			//'date(\'g\')' => '12-hour format of an hour without leading zeros (1 through 12)',
			//'date(\'G\')' => '24-hour format of an hour without leading zeros (0 through 23)',
			//'date(\'h\')' => '12-hour format of an hour with leading zeros (01 through 12)',
			'H' => '24-hour format of an hour with leading zeros (00 through 23)',
			'i' => 'Minutes with leading zeros (00 to 59)',
			's' => 'Seconds, with leading zeros (00 through 59)',
			///'date(\'u\')' => 'Microseconds (654321)',
			'class:Condition,function:amiathome,parameter:\'\'' => 'Am i at Home',
			///'Task::execute($from_device_id,$to_device_id,$action_id)' => 'Task, {id} {name}',
		]
	];
	
	public $weights = [];

	public function init() {
		// add all task
		//$this->values['task'] = Task::getAllIdId();
		
		// add all task defined
		$this->values['taskdefined'] = TaskDefined::getAllIdName();
		
		// add all setting
		$this->values['setting'] = Setting::getAllIdName();
		
		// translate all
		foreach ($this->conditions_actions as $condition_action => $name){
			$this->conditions_actions[$condition_action] = Yii::t('app', $name);
		}
		
		// create weights
		$key = 0;
		foreach($this->getRuleAllIdName() as $id => $name){
			$this->weights[$key] = $key;
			$key++;
		}
		
		$this->weights[$key] = $key;
		
		// put all keys from date before description
		foreach ($this->values['date'] as $key => $description){
			$this->values['date'][$key] = '(' . $key . ') ' . $description;
		}
		
		parent::init();
	}
		
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'weight'], 'required'],
            [['description'], 'string'],
						[['weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255]
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
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return RuleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RuleQuery(get_called_class());
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
		
		public function getRuleAllIdName(){
			return ArrayHelper::map(Rule::find()->asArray()->all(), 'id', 'name');
		}
}
