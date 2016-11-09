<?php

namespace admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use admin\models\Rate;
use admin\models\Employee;
use admin\models\Criterion;

/**
 * RateController implements the CRUD actions for Rate model.
 */
class RateController extends Controller
{
    /**
     * Displays a single Rate model.
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
     * Updates an existing Rate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $employees = ArrayHelper::map(Employee::find()->select(['id', new \yii\db\Expression("CONCAT(`name`, ' ', `surname`) as name")])
            ->asArray()
            ->all(), 'id','name');

        $criteria = ArrayHelper::map(Criterion::find()->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `abbr`,')') as name")])
            ->asArray()
            ->all(), 'id','name');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'employees' => $employees,
                'criteria' => $criteria
            ]);
        }
    }

    /**
     * Finds the Rate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
