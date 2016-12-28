<?php

namespace rest\versions\v1\controllers;

use common\models\Mailer;
use common\models\profile\Type;
use common\models\Theme;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use kartik\mpdf\Pdf;

use common\models\Factory;
use common\models\Stock;
use common\models\stock\Reply;
use common\models\factory\Product;
use yii\web\UploadedFile;

class FactoryController extends Controller
{

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return new ActiveDataProvider([
            'query' => Factory::getForCurrentUser(),
        ]);
    }

    public function actionThemes()
    {
        $ids = Theme::find()->select('factory_id')->asArray();
        return Factory::find()->where(['in', 'id', $ids])->all();
    }

    public function actionAll() {
        if (Yii::$app->user->identity->type_id == Type::TYPE_PHARMACIST) {
            $query = Factory::find()->where(['!=', 'id', 10])
                ->andWhere(['is_shown' => 1])
                ->andWhere(Yii::$app->user->identity->inList.'=2');
        } elseif (Yii::$app->user->identity->type_id == Type::TYPE_AGENT) {
            $query = Factory::find()->where(['id' => Yii::$app->user->identity->agent->factory_id]);
        }
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function actionView($id) {
        return Factory::findOne($id);
    }

    public function actionProducts($factory_id) {
        return Factory::findOne($factory_id)->products;
    }

    public function actionProduct($id) {
        return Product::findOne($id);
    }

    public function actionStocks($factory_id) {
        return new ActiveDataProvider([
            'query' => Stock::getForCurrentUser()->andWhere(['factory_id'=>$factory_id]),
        ]);
    }

    public function actionStock($id) {
        return Stock::getOneForCurrentUser($id);
    }

    public function actionReply()
    {
        $reply = new Reply();

        if($reply->load(Yii::$app->request->post(),'')) {
                $reply->image = UploadedFile::getInstance($reply, 'image');
                $reply->user_id = Yii::$app->user->id;
                if ($reply->validate()) {
                    $reply->saveImage();
                    $reply->save(false);
                    $this->sendPdf($reply, $reply->user);
                    return ['success'=>true];
                }
        }
        return $reply;
    }

    private function sendPdf($reply, $user)
    {
        $filename = 'info.pdf';
        $pdf = new Pdf([
            'content' => $this->renderPartial('pdf-stock-reply', ['user' => $user]),
            'options' => [
                'title' => 'Информация о пользователе',
            ],
            'filename' => Yii::getAlias('@uploads/temp/'.$filename),
            'destination' => Pdf::DEST_FILE,
        ]);
        $pdf->render();
        Mailer::sendStockReply(Yii::getAlias('@uploads_view/temp/'.$filename), $reply);
        @unlink(Yii::getAlias('@uploads/temp/'.$filename));
    }

}