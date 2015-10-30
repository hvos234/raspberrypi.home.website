<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ConditionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="condition-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Condition'), ['create'], ['class' => 'btn btn-success']) ?>
				<?= Html::a(Yii::t('app', 'Am I at Home'), ['amiathome'], ['class' => 'btn btn-success']) ?>
    </p>
		
		<p>
			<?= Yii::$app->session->getFlash('amiathome'); ?>
		</p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'condition',
            'equation',
            'value',
            // 'created_at',
            // 'updated_at',

            //['class' => 'yii\grid\ActionColumn'],
						[
							'class' => 'yii\grid\ActionColumn', 
							//'controller' => 'task-defined',
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
