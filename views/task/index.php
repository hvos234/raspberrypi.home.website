<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Task Defineds');
//$this->params['breadcrumbs'][] = $this->title;

use app\models\Setting;

$path_script_task = Setting::getOneByName('path_script_task');
var_dump($path_script_task);
echo('$path_script_task: ' . $path_script_task['data']) . '<br/>' . PHP_EOL;

$command = 'sudo ' . $path_script_task['data'] . ' 1 4 2';
exec(escapeshellcmd($command), $output, $return_var);

var_dump($return_var);
echo('$output: <pre>');
print_r($output);
echo('</pre>');



?>
<div class="task-defined-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Task Defined'), ['task-defined/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProviderTaskDefined,
        'filterModel' => $searchModelTaskDefined,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'from_device_id',
            'to_device_id',
            'action_id',
            //'created_at',
            //'updated_at',

            [
							'class' => 'yii\grid\ActionColumn', 
							'controller' => 'task-defined',
							'template' => '{view} {update} {delete} {execute}',
							'buttons' => [
								'execute' => function ($url, $model, $key) {
									return Html::a('execute', $url);
								},
							],
						],
					],
    ]); ?>

</div>
<?php

$this->title = Yii::t('app', 'Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Task'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'from_device_id',
            'to_device_id',
            'action_id',
            'data',
            'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
