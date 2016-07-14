<?php

namespace rest\versions\v1\controllers;


use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use rest\components\Controller;
use common\models\Theme;
use common\models\theme\Reply;
use common\models\forms\Answer;
use common\models\User;
use common\models\Mailer;
use kartik\mpdf\Pdf;
use yii\web\UploadedFile;

class ThemeController extends Controller
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

    public function actionCompany($factory_id)
    {
        return new ActiveDataProvider([
            'query' => Theme::find()->where(['factory_id' => $factory_id]),
        ]);
    }

    public function actionView($id)
    {
        return  Theme::find()->where(['id' => $id])->one();
    }


    public function actionSend()
    {

        $reply = new Reply();
        if($reply->load(Yii::$app->request->post(),'')) {
            $reply->image = UploadedFile::getInstance($reply, 'image');
            $reply->user_id = Yii::$app->user->id;
            if($reply->validate()) {
                $form = [new Answer()];
                for($i = 1; $i < count($_POST['answer']); $i++) {
                    $form[] = new Answer();
                }

                if(Answer::loadMultiple($form,$_POST,'answer')) {
                    $form = Answer::filterModels($form);
                    if(Answer::validateMultiple($form,['field_id','value'])) {
                        $user = User::findOne($reply->user_id);
                        $theme = Theme::findOne($reply->theme_id);
                        $this->sendPdf($user, $theme);
                        return ['success'=>true];
                    }
                }

                return $form;
            }
        }
        return $reply;
    }

    private function sendPdf($user, $theme)
    {
        $filename = 'Answer-'.Yii::$app->security->generateRandomString(5).'.pdf';
        $pdf = new Pdf([
            'content' => $this->renderPartial('pdf-export', ['user' => $user, 'theme' => $theme]),
            'options' => [
                'title' => 'Ответ на тему',
                'subject' => 'Заполненная форма темы',
                'defaultfooterline'=>false,
                'margin_footer'=>0,
            ],
            'cssInline'=>file_get_contents('../admin/css/pdf-export.css'),
            'marginLeft'=>10,
            'marginTop'=>10,
            'marginRight'=>10,
            'marginBottom'=>10,
            'filename' => Yii::getAlias('@uploads/temp/'.$filename),
            'destination' => Pdf::DEST_FILE,
        ]);
        $pdf->render();
        Mailer::sendThemeAnswer(Yii::getAlias('@uploads_view/temp/'.$filename), $theme->email);
        @unlink(Yii::getAlias('@uploads/temp/'.$filename));
    }

}