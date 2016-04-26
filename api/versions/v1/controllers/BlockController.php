<?php

namespace rest\versions\v1\controllers;

use Yii;

use common\models\Block;
use common\models\block\Comment;
use common\models\block\Mark;

use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class BlockController extends Controller
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

    public function actionIndex($id)
    {
        return Block::findOne($id);
    }

    public function actionComments($block_id)
    {
        return new ActiveDataProvider([
            'query' => Comment::findByBlock($block_id),
        ]);
    }

    public function actionComment($id)
    {
        return Comment::findOne($id);
    }

    public function actionAddComment()
    {
        $comment = new Comment(['scenario'=>'add']);

        if ($comment->load(Yii::$app->request->getBodyParams(), '') && $comment->validate(['block_Id','comment'])) {
            $comment->user_id = Yii::$app->user->id;
            $comment->save(false);
            return ['success'=>true];
        } else return $comment;
    }

    public function actionIsMarkExists($block_id)
    {
        return ['exists'=>Mark::isMarkedByCurrentUser($block_id)];
    }

    public function actionAddMark()
    {
        $mark = new Mark(['scenario'=>'add']);

        $mark->user_id = Yii::$app->user->id;
        if($mark->load(Yii::$app->request->getBodyParams(), '') && $mark->save()) {
            return ['success'=>true];
        } else return $mark;
    }

    public function actionDeleteComment($id) {
        if($comment = Comment::findOne(['id'=>$id,'user_id'=>Yii::$app->user->id])) {
            $comment->delete();
            return ['success'=>true];
        } else {
            return ['success'=>false];
        }
    }
}