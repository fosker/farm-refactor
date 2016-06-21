<?php

namespace app\modules\profile\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\models\profile\UpdateRequest;
use yii\helpers\ArrayHelper;
use app\models\agency\Firm;
use app\models\agency\Pharmacy;
use app\models\location\City;
use app\models\location\Region;
use app\models\profile\Education;
use app\models\profile\Position;
use yii\web\UploadedFile;


class ProfileController extends Controller
{
    public function actionIndex()
    {
        $model = $this->findModel();
        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function actionUpdatePassword()
    {
        $model = new User();
        $model->scenario = 'update-password';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $this->findModel();
            $user->setPassword($model->password);
            $user->save(false);
            return $this->redirect(['/profile']);
        } else {
            return $this->render('update-password', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdateAvatar()
    {
        $model = $this->findModel();
        $model->scenario = 'update-photo';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->image = UploadedFile::getInstance($model, 'image');
            $model->saveImage();
            $model->save(false);
            return $this->redirect(['/profile']);
        } else {
            return $this->render('update-avatar', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdateProfile()
    {
        UpdateRequest::deleteAll(['user_id'=>$this->findModel()->id]);

        $request = new UpdateRequest();
        $model = $this->findModel();
        $model->scenario = 'update-profile';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $request->loadCurrentAttributes($model);
            $request->save();
            return $this->redirect(['/profile']);
        } else {
            return $this->render('update-profile', [
                'model' => $model,
                'firms' => ArrayHelper::map(Firm::find()->asArray()->all(), 'id','name'),
                'regions' => ArrayHelper::map(Region::find()->asArray()->all(), 'id','name'),
                'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
                'education' => ArrayHelper::map(Education::find()->asArray()->all(), 'id','name'),
                'pharmacies' => ArrayHelper::map(Pharmacy::find()
                    ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
                    ->asArray()->all(), 'id','name'),
                'positions' => ArrayHelper::map(Position::find()->asArray()->all(), 'id','name'),
            ]);
        }
    }

    protected function findModel()
    {
        if (($model = User::findOne(Yii::$app->user->id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
    }
}
