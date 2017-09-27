<?php
namespace api\modules\v1\controllers;

use common\models\Chat;
use common\models\ChatUser;
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
                        'all',
                        'create',
                        'delete'
                    ],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'all' => ['get'],
                'create' => ['post'],
                'delete' => ['delete']
            ],
        ];

        return $behaviors;
    }

    // get
    // get all chat for this user
    public function actionAll()
    {
        $models = Chat::find()->where(['or',
            ['created_by' => Yii::$app->user->id],
            ['user_id' => Yii::$app->user->id],
        ])->all();
        //return $model;
        if ($models) {
            return Chat::allFields($models);
        }
        return ['error' => 'Error. No rooms'];
    }

    // post
    // param user_id {кого добавить в чат с текущим пользователем}
    //       name {chat name}
    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $chat = Chat::find()->where(['or',
            [
                'created_by' => $post['user_id'],
                'user_id' => Yii::$app->user->id
            ],
            [
                'created_by' => Yii::$app->user->id,
                'user_id' => $post['user_id']
            ]
        ])->one();

        if (!$chat) {
            $chat = new Chat();
            $chat->user_id = $post['user_id'];

            if (!$chat->save()) {
                return ['errors' => $chat->errors];
            }
        }
        return true;
    }

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



//    public function up()
//    {
//        $this->createIndex('relation_idx', 'relations', ['id_worker', 'id_courier'], true);
//
//        $this->createIndex('fk_coordinates_user_idx', '{{%coordinates}}', 'id_user');
////
////        $this->createIndex('fk_coordinates_user_idx', '{{%coordinates}}', 'id_user');
////        $this->createIndex('fk_coordinates_user_idx', '{{%coordinates}}', 'id_user');
//
//        $this->addForeignKey('fk_coordinate_user', '{{%coordinates}}', 'id_user', '{{%user}}', 'id');
//
//        $this->addForeignKey('fk_relations_user_worker', '{{%relations}}', 'id_worker', '{{%user}}', 'id');
//        $this->addForeignKey('fk_relations_user_courier', '{{%relations}}', 'id_courier', '{{%user}}', 'id');
//    }
//
//    public function down()
//    {
//        $this->dropIndex('relation_idx', 'relations');
//        $this->createIndex('relation_idx', 'relations', ['id_worker', 'id_courier'], true);
//
//        return false;
//    }
}