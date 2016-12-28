<?php

namespace backend\controllers;

use Yii;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\forms\Field;
use common\models\forms\Option;
use common\models\Form;
use backend\base\Model;
use backend\models\form\Search;

class FormController extends Controller
{

    public function actionIndex()
    {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Form;

        $fields = [new Field];
        $options = [[new Option]];

        if ($model->load(Yii::$app->request->post())) {

            $fields = Model::createMultiple(Field::className());
            Model::loadMultiple($fields, Yii::$app->request->post());

            $optionsData['_csrf'] =  Yii::$app->request->post()['_csrf'];
            for ($i=0; $i<count($fields); $i++) {
                $optionsData['Option'] =  Yii::$app->request->post()['Option'][$i];
                $options[$i] = Model::createMultiple(Option::classname(),[] ,$optionsData);
                Model::loadMultiple($options[$i], $optionsData);
            }
            if ($model->validate()) {
                if ($this->saveForm($model,$fields,$options)) {
                    return $this->redirect(['view', 'id'=>$model->id]);
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
            'fields' => (empty($questions)) ? [new Field] : $fields,
            'options' => (empty($options)) ? [new Option] : $options,
        ]);
    }

    protected function saveForm($model,$fields,$options ) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($go = $model->save(false)) {
                // loop through each question
                foreach ($fields as $i => $field) {
                    // save the question record
                    $field->form_id = $model->id;
                    if ($go = $field->save(false)) {
                        // loop through each option
                        foreach ($options[$i] as $id => $option) {
                            // save the option record
                            $option->field_id = $field->id;
                            if (! ($go = $option->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }
            }
            if ($go) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return $go;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (($model = Form::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Форма не найдена.');
        }
    }
}
