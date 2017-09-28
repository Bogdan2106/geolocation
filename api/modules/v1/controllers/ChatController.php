<?php
namespace api\modules\v1\controllers;

use common\models\Chat;
use common\models\ChatUser;
use common\models\Relations;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\auth\QueryParamAuth;

use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * Site controller
 */
class ChatController extends ActiveController
{
    public $modelClass = 'common\models\Chat';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'auth_key',
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            /*'only' => [
                'all',
                'create',
                'delete'
            ],*/
            'rules' => [
                [
                    'actions' => [
                        'one',
                        'create',
                        'delete',
                        'one-chat'
                    ],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'one' => ['get'],
                'create' => ['post'],
                'one-chat' => ['GET'],
                'delete' => ['delete']
            ],
        ];

        return $behaviors;
    }
//
//    public function actionOneChat()
//    {
////        $model = Chat::findAll(Yii::$app->user->id);
////        if($model) {
////            return Chat::roomFields($model);
////        }
//
//        $model = Chat::find()->where(['or',
//            ['created_by' => Yii::$app->user->id],
//            ['user_id' => Yii::$app->user->id],
//        ])->all();
//        return Chat::roomFields($model);
//    }


    // get
    public function actionOne($id)
    {
        $user = User::findOne($id);

        if (!$user) {
            return ['errors' => 'User 404. Not found'];
        }

        $isCourier = (int) Relations::find()->where([
            'id_worker' => Yii::$app->user->id,
            'id_courier' => $id,
        ])->count();

        if (!$isCourier) {
            return ['errors' => "'$user->username' не ваш курьер"];
        }

        $room = Chat::find()->where(['or',
            [
                'created_by' => $id,
                'user_id' => Yii::$app->user->id
            ],
            [
                'created_by' => Yii::$app->user->id,
                'user_id' => $id
            ]
        ])->one();

        if ($room) {
            return Chat::roomFields($room);
        }

        return ['error' => 'Error. No room'];
    }

    // post
    // param user_id {кого добавить в чат с текущим пользователем}
    //       name {chat name}
//    public function actionCreate()
//    {
//        $post = Yii::$app->request->post();
//        $chat = Chat::find()->where(['or',
//            [
//                'created_by' => $id,
//                'user_id' => Yii::$app->user->id
//            ],
//            [
//                'created_by' => Yii::$app->user->id,
//                'user_id' => $id
//            ]
//        ])->one();
//
//        if (!$chat) {
//            $chat = new Chat();
//            $chat->user_id = $post['user_id'];
//
//            if (!$chat->save()) {
//                return ['errors' => $chat->errors];
//            }
//        }
//        return true;
//    }

    // get | delete
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$model) {
            return ['errors' => 'not found'];
        }

        if (!$model->delete()) {
            return ['errors' => $model->errors];
        }

        return true;
    }

    protected function findModel($id)
    {
        if (($model = Chat::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}