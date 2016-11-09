<?php

namespace admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\base\Model;

use admin\models\Employee;
use admin\models\search\employee\Search;
use admin\models\Department;
use admin\models\Position;
use admin\models\Criterion;
use admin\models\Rate;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departments' => ArrayHelper::map(Department::find()->asArray()->all(), 'id', 'name'),
            'positions' => ArrayHelper::map(Position::find()->asArray()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $rates = Rate::findAll(['employee_id' => $id]);
        $criteria = Criterion::find()->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'rates' => $rates,
            'criteria' => $criteria
        ]);
    }

    /**
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'departments' => ArrayHelper::map(Department::find()->asArray()->all(), 'id', 'name'),
                'positions' => ArrayHelper::map(Position::find()->asArray()->all(), 'id', 'name'),
            ]);
        }
    }

    /**
     * Updates an existing Employee model.
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
                'departments' => ArrayHelper::map(Department::find()->asArray()->all(), 'id', 'name'),
                'positions' => ArrayHelper::map(Position::find()->asArray()->all(), 'id', 'name'),
            ]);
        }
    }

    /**
     * Deletes an existing Employee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionRate($id)
    {
        $model = $this->findModel($id);
        $criteria = Criterion::find()->all();
        $rates = [];
        foreach ($criteria as $item) {
            $rates[$item->id] = new Rate();
        }

        if (Model::loadMultiple($rates, Yii::$app->request->post())) {
            foreach ($rates as $index => $rate) {
                $rate->employee_id = $id;
                $rate->criterion_id = $index;
                $rate->save();
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('rate', [
                'model' => $model,
                'criteria' => $criteria,
                'rates' => $rates
            ]);
        }
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
