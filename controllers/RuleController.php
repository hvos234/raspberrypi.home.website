<?php

namespace app\controllers;

use Yii;
use app\models\Rule;
use app\models\RuleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

use app\models\RuleCondition;

/**
 * RuleController implements the CRUD actions for Rule model.
 */
class RuleController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
						// this will allow authenticated users to access the create update and delete
						// and deny all other users from accessing these three actions.
						'access' => [
							'class' => AccessControl::className(),
							//'only' => ['create', 'update', 'delete'],
							'rules' => [
									// deny all POST requests
									/*[
											'allow' => false,
											'verbs' => ['POST'],
									],*/
									// allow authenticated users
									[
											'allow' => true,
											'roles' => ['@'],
									],
									// everything else is denied
							],
						],
					];
    }

    /**
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RuleSearch();
				$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
				$dataProvider->setSort([
					'defaultOrder' => ['weight' => SORT_ASC]
				]);
				
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rule model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Rule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Rule();
				
				// create 10 RuleCondition models
				$modelsRuleCondition[] = new RuleCondition();
				
				if($model->load(Yii::$app->request->post()) && RuleCondition::loadMultiple($modelsRuleCondition, Yii::$app->request->post()) && isset(Yii::$app->request->post()['RuleCondition_add'])){
					var_dump(Yii::$app->request->post());
					exit();
					
					$modelsRuleCondition[count(Yii::$app->request->post()['RuleCondition'])] = new RuleCondition();
					return $this->render('create', [
							'model' => $model,
							'modelsRuleCondition' => $modelsRuleCondition,
					]);
				}
				
				if($model->load(Yii::$app->request->post()) && RuleCondition::loadMultiple($modelsRuleCondition, Yii::$app->request->post()) && isset(Yii::$app->request->post()['RuleCondition_remove'])){
					//$modelsRuleCondition = RuleCondition::loadMultiple($modelsRuleCondition, Yii::$app->request->post());

					//var_dump(Yii::$app->request->post()['RuleCondition_remove']);
					/*exit();*/
					if(1 > count(Yii::$app->request->post()['RuleCondition'])){
						unset($modelsRuleCondition[count(Yii::$app->request->post()['RuleCondition_remove'])]);
					}
					
					/*echo('key: ' . key(Yii::$app->request->post()['RuleCondition_remove']));
					
					var_dump(Yii::$app->request->post());
					var_dump($modelsRuleCondition);
					exit();*/
					
					return $this->render('create', [
							'model' => $model,
							'modelsRuleCondition' => $modelsRuleCondition,
					]);
				}
				
				if(!isset(Yii::$app->request->post()['RuleCondition_add']) && !isset(Yii::$app->request->post()['RuleCondition_remove'])){
					if($model->load(Yii::$app->request->post()) && RuleCondition::loadMultiple($modelsRuleCondition, Yii::$app->request->post())){
						$isValid = $model->validate();
						$isValid = RuleCondition::validateMultiple($modelsRuleCondition) && $isValid;
						if ($isValid) {
							$model->save(false);
							foreach ($modelsRuleCondition as $modelRuleCondition) {
								$modelRuleCondition->rule_id = $model->id;
								$modelRuleCondition->save(false);
							}
						}
					}else {
						return $this->render('create', [
								'model' => $model,
								'modelsRuleCondition' => $modelsRuleCondition,
						]);
					}
				}
    }

    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Rule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Rule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
