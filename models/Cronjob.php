<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\models\TaskDefined;
use app\models\TaskDefinedSearch;

/**
 * This is the model class for table "{{%cronjob}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $recurrence_minutes
 * @property string $recurrence_hour
 * @property string $recurrence_day
 * @property string $recurrence_week
 * @property string $recurrence_month
 * @property string $recurrence_year
 * @property string $job
 * @property integer $job_id
 * @property string $start_at
 * @property string $end_at
 * @property string $run_at
 * @property string $created_at
 * @property string $updated_at
 */
class Cronjob extends \yii\db\ActiveRecord
{
		public $recurrence_minutes = [
			'0 minute' => '0',
			'5 minutes' => '5',
			'10 minutes' => '10',
			'15 minutes' => '15',
			'20 minutes' => '20',
			'25 minutes' => '25',
			'30 minutes' => '30',
			'40 minutes' => '40',
			'50 minutes' => '50',
		];
		
		public $recurrence_hours = [
			'0 hour' => '0',
			'1 hour' => '1',
			'2 hours' => '2',
			'3 hours' => '3',
			'4 hours' => '4',
			'5 hours' => '5',
			'6 hours' => '6',
			'12 hours' => '12',
			'18 hours' => '18',
		];
		
		public $recurrence_days = [
			'0 day' => '0',
			'1 day' => '1',
			'2 days' => '2',
			'3 days' => '3',
			'4 days' => '4',
			'5 days' => '5',
			'6 days' => '6',
		];
		
		public $recurrence_weeks = [
			'0 week' => '0',
			'1 week' => '1',
			'2 weeks' => '2',
			'3 weeks' => '3',
			'4 weeks' => '4',
		];
		
		public $recurrence_months = [
			'0 month' => '0',
			'1 month' => '1',
			'2 months' => '2',
			'3 months' => '3',
			'4 months' => '4',
			'5 months' => '5',
			'6 months' => '6',
			'7 months' => '7',
			'8 months' => '8',
			'9 months' => '9',
			'10 months' => '10',
			'11 months' => '11',
		];
		
		public $recurrence_years = [
			'0 year' => '0',
			'1 year' => '1',
			'2 years' => '2',
			'3 years' => '3',
			'4 years' => '4',
			'5 years' => '5',
			'6 years' => '6',
			'7 years' => '7',
			'8 years' => '8',
			'9 years' => '9',
			'10 years' => '10',
		];
		
		public $jobs = [
			'task' => 'Task',
			'rule' => 'Rule',
		];
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cronjob}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'recurrence_minute', 'recurrence_hour', 'recurrence_day', 'recurrence_week', 'recurrence_month', 'recurrence_year', 'job', 'task_id', 'rule_id', 'start_at'], 'required'],
            [['description'], 'string'],
            [['start_at', 'end_at', 'run_at', 'created_at', 'updated_at'], 'safe'],
            [['task_id', 'rule_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['recurrence_minute', 'recurrence_hour', 'recurrence_day', 'recurrence_week', 'recurrence_month', 'recurrence_year'], 'string', 'max' => 20],
            [['job'], 'string', 'max' => 32]
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
            'description' => Yii::t('app', 'Description'),
            'recurrence_minute' => Yii::t('app', 'Minutes'),
            'recurrence_hour' => Yii::t('app', 'Hours'),
            'recurrence_day' => Yii::t('app', 'Days'),
            'recurrence_week' => Yii::t('app', 'Weeks'),
            'recurrence_month' => Yii::t('app', 'Months'),
            'recurrence_year' => Yii::t('app', 'Years'),
            'job' => Yii::t('app', 'Job'),
            'task_id' => Yii::t('app', 'Task Name'),
            'rule_id' => Yii::t('app', 'Rule Name'),
            'start_at' => Yii::t('app', 'Start At'),
            'end_at' => Yii::t('app', 'End At'),
            'run_at' => Yii::t('app', 'Run At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return CronjobQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CronjobQuery(get_called_class());
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
		 * This function is call by app\commands\CronController, 
		 * the CronController is call by the server cron
		 */
		public function cron(){
			$model = new Cronjob();
			$cronjobs = $model->getCronjobAll();
			
			// define date, and floor to 5 minutes
			$now = date('Y-m-d H:i:00', floor(time() / (5 * 60)) * (5 * 60));
			
			foreach($cronjobs as $cronjob){
				
				// check start_at and end_at is between date now
				if($cronjob['start_at'] <= $now and ($cronjob['end_at'] >= $now or empty($cronjob['end_at']))){
				
					// add the cronjob time to the run_at, and check if it has to run (has to be lower or equal than now)
					$run_at = $cronjob['run_at'];
					
					$run_at = date('Y-m-d H:i:s', strtotime('+' . $cronjob['recurrence_minute'], strtotime($run_at)));
					$run_at = date('Y-m-d H:i:s', strtotime('+' . $cronjob['recurrence_hour'], strtotime($run_at)));
					$run_at = date('Y-m-d H:i:s', strtotime('+' . $cronjob['recurrence_day'], strtotime($run_at)));
					$run_at = date('Y-m-d H:i:s', strtotime('+' . $cronjob['recurrence_week'], strtotime($run_at)));
					$run_at = date('Y-m-d H:i:s', strtotime('+' . $cronjob['recurrence_month'], strtotime($run_at)));
					$run_at = date('Y-m-d H:i:s', strtotime('+' . $cronjob['recurrence_year'], strtotime($run_at)));
					
					// check if it has to run
					if($run_at <= $now){
					
						switch($cronjob['job']){
							case 'task':
								$modelTaskDefined = new TaskDefined();
								$modelTaskDefined->execute($cronjob['task_id']);
						}
						
						// update run_at
						$model = Cronjob::findOne($cronjob['id']);						
						$model->run_at = $now;
						if (!$model->save()){ 
							print_r($model->errors);
						}
					}
				}
			}
		}
		
		public function getCronjobAll(){
			// get all the actions
			return Cronjob::find()->asArray()->all();
		}	
}
