<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%condition}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $condition
 * @property string $equation
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 */
class Condition extends \yii\db\ActiveRecord
{
	public $conditions = [
		// date
		'{"class":"php","function":"date","parameter":"d"}' => 'Day of the month, 2 digits with leading zeros (01 to 31)',
		//'date(\'j\')' => 'Day of the month without leading zeros (1 to 31)',
		'{"class":"php","function":"date","parameter":"D"}' => 'A textual representation of a day, three letters (Mon through Sun)',
		'{"class":"php","function":"date","parameter":"l"}' => 'A full textual representation of the day of the week (Sunday through Saturday)',
		'{"class":"php","function":"date","parameter":"N"}' => 'ISO-8601 numeric representation of the day of the week (1 (for Monday) through 7 (for Sunday))',
		//'date(\'w\')' => 'Numeric representation of the day of the week (0 (for Sunday) through 6 (for Saturday))',
		//'date(\'z\')' => 'The day of the year (starting from 0 through 365)',
		'{"class":"php","function":"date","parameter":"W"}' => 'ISO-8601 week number of year, weeks starting on Monday (42 is the 42nd week in the year)',
		'{"class":"php","function":"date","parameter":"m"}' => 'Numeric representation of a month, with leading zeros (01 through 12)',
		//'date(\'n\')' => 'Numeric representation of a month, without leading zeros (1 through 12)',
		'{"class":"php","function":"date","parameter":"F"}' => 'A full textual representation of a month (January through December)',
		'{"class":"php","function":"date","parameter":"M"}' => 'A short textual representation of a month, three letters (Jan through Dec)',
		///'date(\'t\')' => 'Number of days in the given month (28 through 31)',
		'{"class":"php","function":"date","parameter":"Y"}' => 'A full numeric representation of a year, 4 digits (1999 or 2003)',
		//'date(\'y\')' => 'A two digit representation of a year (99 or 03)',
		///'date(\'L\')' => 'Whether it's a leap year (1 if it is a leap year, 0 otherwise)',
		///'date(\'o\')' => 'ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead (1999 or 2003)',
		///'date(\'a\')' => 'Lowercase Ante meridiem and Post meridiem (am or pm)',
		///'date(\'A\')' => 'Uppercase Ante meridiem and Post meridiem (AM or PM)',
		///'date(\'B\')' => 'Swatch Internet time (000 through 999)',
		//'date(\'g\')' => '12-hour format of an hour without leading zeros (1 through 12)',
		//'date(\'G\')' => '24-hour format of an hour without leading zeros (0 through 23)',
		//'date(\'h\')' => '12-hour format of an hour with leading zeros (01 through 12)',
		'{"class":"php","function":"date","parameter":"H"}' => '24-hour format of an hour with leading zeros (00 through 23)',
		'{"class":"php","function":"date","parameter":"i"}' => 'Minutes with leading zeros (00 to 59)',
		'{"class":"php","function":"date","parameter":"s"}' => 'Seconds, with leading zeros (00 through 59)',
		///'date(\'u\')' => 'Microseconds (654321)',
		'{"class":"Condition","function":"amiathome","parameter":[]}' => 'Am i at Home',
		///'Task::execute($from_device_id,$to_device_id,$action_id)' => 'Task, {id} {name}',
		
	];
	public $equations = [
		'==' => 'Equal',
		'!=' => 'Not equal',
		'>=' => 'Bigger or Equal', 
		'<=' => 'Smaller or Equal', 
		'>' => 'Bigger', 
		'<' => 'Smaller',
	];

	public function init() {
		// add all task defined as Task::execute to the conditions
		$modelTaskDefined = new TaskDefined();
		$tasksdefined = $modelTaskDefined->getTaskDefinedAll();
		
		foreach($tasksdefined as $taskdefined){
			$this->conditions[sprintf('{"class":"Task","function":"execute","parameter":["%d","%d","%d"]}', $taskdefined['from_device_id'], $taskdefined['to_device_id'], $taskdefined['action_id'])] = sprintf('(%d) %s', $taskdefined['id'], $taskdefined['name']);
		}
		
		// translate all conditions
		foreach ($this->conditions as $json => $condition){
			$this->conditions[$json] = Yii::t('app', $condition);
		}
		
		// add date, condition or task before value condition
		foreach ($this->conditions as $json => $condition){
			$array = json_decode($json, true);
			
			switch($array['class']){
				case 'php':
					switch($array['function']){
						case 'date':
							$this->conditions[$json] = sprintf('date(\'%s\'), %s', $array['parameter'], $condition);
							break;
					}
					break;
				
				default:
					$this->conditions[$json] = sprintf('%s, %s', $array['class'], $condition);
			}
		}
		
		// translate all equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = Yii::t('app', $equation);
		}
		
		// key before value equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = $key . ', ' .  $equation;
		}
		
		parent::init();
	}

		/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%condition}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'condition', 'equation', 'value'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'value'], 'string', 'max' => 255],
            [['condition'], 'string', 'max' => 128],
            [['equation'], 'string', 'max' => 4]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'condition' => Yii::t('app', 'Condition'),
            'equation' => Yii::t('app', 'Equation'),
            'value' => Yii::t('app', 'Value'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ConditionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConditionQuery(get_called_class());
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
		
		/**
		 * amiathome
		 * Is a static function so i can use it in the conditions
		 * 
		 * @return boolean
		 */
		public static function amiathome(){
			$ip = '192.168.192.3';
			
			$cmd = sprintf('ping -c 2 -w 2 %s', $ip); // -c number of times, and -w timeout in seconds
			exec(escapeshellcmd($cmd), $output, $return_code);
			
			if(0 == $return_code) { 
				return true;
			}
			
			return false;
		}
		
		/**
		 * 
		 * @param type $id
		 * @return boolean
		 */
		public function execute($id){
			$model = Condition::findOne($id);
			
			/*$date = [
				'class' => 'php',
				'function' => 'date',
				'parameter' => 'Y',
			];
			
			echo('$date: ' . json_encode($date)) . '<br/>' . PHP_EOL;
			
			$class = [
				'class' => 'Task',
				'function' => 'execute',
				'parameter' => array('1','4','3'),
			];
			
			echo('$class: ' . json_encode($class)) . '<br/>' . PHP_EOL;
			
			$class = [
				'class' => 'Condition',
				'function' => 'amiathome',
				'parameter' => '',
			];
			
			echo('$class: ' . json_encode($class)) . '<br/>' . PHP_EOL;*/
			
			echo('$model->condition: ' . $model->condition) . '<br/>' . PHP_EOL;
			
			$array = json_decode($model->condition, true);
			echo('<pre>');
			print_r($array);
			echo('</pre>');
			
			$value = '';
			switch($array['class']){
				case 'php':
					switch($array['function']){
						case 'date':
							$value = date($array['parameter']);
							break;
					}
					break;
				
				default:
					echo('app\models\\' . $array['class']);
					$value = call_user_func_array(array('app\models\\' . $array['class'], $array['function']), $array['parameter']);
					//$this->conditions[$json] = sprintf('%s, %s', $array['class'], $condition);
			}
			echo('var_dump'). '<br/>' . PHP_EOL;
			var_dump(json_decode($value));
			echo('$value: ' . $value) . '<br/>' . PHP_EOL;
			
			/*$condition = '';	
			//$condition = eval('$condition = ' . $model->condition . ';');
			$condition = 'Task->execute(1,4,3)';
			
			
			
			$value = call_user_func(array('app\models\Condition', 'amiathome'));
			var_dump($value);
			$value = call_user_func(array('app\models\Condition', 'amiathome'));
			var_dump($value);
			//eval('use app\models\Condition; && $condition =' . $model->condition . '();');
			//$condition = call_user_func($model->condition);
			echo('$condition: ' . $condition) . '<br/>' . PHP_EOL;*/
			exit();
		}
}
