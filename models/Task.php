<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property integer $id
 * @property integer $from_device_id
 * @property integer $to_device_id
 * @property integer $action_id
 * @property string $data
 * @property string $created_at
 */
class Task extends \yii\db\ActiveRecord
{			
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_device_id', 'to_device_id', 'action_id'], 'required'],
            [['from_device_id', 'to_device_id', 'action_id'], 'integer'],
            [['created_at', 'data'], 'safe'],
						[['data'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'from_device_id' => Yii::t('app', 'From Device ID'),
            'to_device_id' => Yii::t('app', 'To Device ID'),
            'action_id' => Yii::t('app', 'Action ID'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }
		
		/**
		 * Auto add date time to created_at and updated_at
		 */
		// only works if both created_at and updated_at exist !
		public function behaviors()
		{
			return [
					// This set the create_at and updated_at by create, and 
					// update_at by update, with the date time / timestamp
					[
						'class' => TimestampBehavior::className(),
						'createdAtAttribute' => 'created_at',
						'updatedAtAttribute' => false, // this must be set to false or it rease a error that update_at does not exist
						'value' => new Expression('NOW()'),
					],
			 ];
		}
		
		public function beforeSave($insert){
			if($insert){ // if true it is inserted if false it is updated
				$this->data = Task::execute($this->from_device_id, $this->to_device_id, $this->action_id);
			}
			
			return parent::beforeSave($insert);
		}
		
		/**
		 * execute
		 * Is a static function so i can use it in the conditions
		 * 
		 * @param type $from_device_id
		 * @param type $to_device_id
		 * @param type $action_id
		 * @return type
		 */
		public static function execute($from_device_id, $to_device_id, $action_id){
			// sudo visudo and add exception www-data ALL=(ALL) NOPASSWD: ALL, and use sudo
			$cmd = 'sudo python /var/www/html/home/commands/Task.py ' . $from_device_id . ' ' . $to_device_id . ' ' . $action_id;
			exec(escapeshellcmd($cmd), $output, $return_code);
			
			// if nothing is returned, or a error
			$output = end($output);
			if(empty($output) or 0 === strpos($output, 'error')){
				// retry
				exec(escapeshellcmd($cmd), $output, $return_code);
				$output = end($output);
			}
			
			
			return $output;
		}
		
		public function getTaskBetweenDate($between, $from_device_id = '', $to_device_id = '', $action_id = ''){
			$where = [];
			if(!empty($from_device_id)){
				$where['from_device_id'] = $from_device_id;
			}
			if(!empty($to_device_id)){
				$where['to_device_id'] = $to_device_id;
			}
			if(!empty($action_id)){
				$where['action_id'] = $action_id;
			}
			$tasks = Task::find()->where($where)->andwhere(['between', 'created_at', $between['from'], $between['to']])->asArray()->all();
			foreach ($tasks as $key => $task){
				$tasks[$key]['data'] = $this->data_encode($task['data']);
			}
			
			return $tasks;
		}
		
		public function data_encode($data){
			return json_encode($data, true);
		}
}
