<?php

namespace api\modules\v1\controllers;

use common\models\Chat;
use common\models\Coordinates;
use common\models\Relations;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use backend\models\UserSearch;
use common\models\LoginForm;
use common\models\User;
use yii\web\NotFoundHttpException;
use yii\filters\auth\HttpBasicAuth;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'auth_key',
            'except' => [
                'login'
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => [
                        'all',
                        'add-courier',
                        'delete-courier',
                        'get-couriers',
                    ],
                    'allow' => true,
                    'roles' => ['worker'],
                ],

                [
                    'actions' => [
                        'get-coordinates',
                        'all-users',
                        'one-user',
                        'set-coordinates',
                        'info-user'

                    ],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => [
                        'login',
                        'login-auth'

                    ],
                    'allow' => true,
                ],
            ],
        ];

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'all' => ['GET'],
                'login-auth' => ['GET'],
                'login' => ['POST'],
                'set-coordinates' => ['POST'],
                'get-coordinates' => ['GET'],
                'get-couriers' => ['GET'],
                'add-courier' => ['GET'],
                'delete-courier' => ['GET'],
                'all-users' => ['GET'],
                'one-user' => ['GET'],
                'info-user' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionAll()
    {
        $model = User::find()->where(['role' => 4])->all();
        //return $model;
        if ($model) {
            return User::allFields($model);
        } else {
            return ['error' => 'Error. Bad request.'];
        }
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post(), "")) {
            if ($model->login()) {
                $result = Yii::$app->user->identity->oneFields();
                return $result;
            }
            return ['error' => 'Invalid login or password'];
        }
        return ['error' => 'Error. Bad request.'];
    }

    public function actionLoginAuth()
    {
        return Yii::$app->user->identity->oneFields();
    }

    public function actionGetCouriers()
    {
        $user = User::findOne(Yii::$app->user->id);
        return User::allFields($user->couriers);
    }

    public function actionAddCourier($id)
    {
        $user = User::findOne($id);

        if (!$user) {
            return ['errors' => 'User 404. Not found'];
        }

        $model = Relations::find()->where([
            'id_worker' => Yii::$app->user->id,
            'id_courier' => $id,
        ])->one();

        if (Yii::$app->user->id == $id) {
            return ['errors' => 'You id == get id'];
        }

        if (!$model) {
            $model = new Relations();
            $model->id_worker = Yii::$app->user->id;
            $model->id_courier = $id;

            if (!$model->save()) {
                return ['errors' => $model->errors];
            }
        }
        // create chat
        $chat = Chat::find()->where(['or',
            [
                'created_by' => $id,
                'user_id' => Yii::$app->user->id
            ],
            [
                'created_by' => Yii::$app->user->id,
                'user_id' => $id
            ]
        ])->one();

        if (!$chat) {
            $chat = new Chat();
            $chat->user_id = $id;
           // $chat->name = ''; // need test

            if (!$chat->save()) {
                return ['errors' => $chat->errors];
            }
        }
        return true;
    }

    public function actionDeleteCourier($id)
    {
        $model = Relations::find()->where([
            'id_worker' => Yii::$app->user->id,
            'id_courier' => $id,
        ])->one();

        if (Yii::$app->user->id == $id) {
            return ['errors' => 'You id == get id'];
        }

        if (!$model) {
            return ['errors' => 'not found'];
        }

        if (!$model->delete()) {
            return ['errors' => $model->errors];
        }

        return true;
    }

    public function actionSetCoordinates()
    {
        $post = Yii::$app->request->post();
        $model = Coordinates::find()->where(['id_user' => Yii::$app->user->id])->one();

        if (!$model) {
            $model = new Coordinates();
            $model->id_user = Yii::$app->user->id;
        }

        if ($model->load($post, '') && $model->save()) {
            return true;
        } else {
            return ['errors' => $model->errors];
        }
    }

    public function actionGetCoordinates($id)
    {
        $model = Coordinates::find()->where(['id_user' => $id])->one();

        if (!$model) {
            return ['errors' => 'User not found'];
        }

        return [
            'lat' => $model->lat,
            'lng' => $model->lng,
        ];
    }

    public function actionSet()
    {
//        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
//        if ($user->getAttribute('role') == 4)
//            return false;

        $post = Yii::$app->request->post();
        $model = Coordinates::find()->where(['id_user' => Yii::$app->user->id])->one();

        if (!$model) {
            $model = new Coordinates();
            $model->id_user = Yii::$app->user->id;
        }

        if ($model->load($post, '') && $model->save()) {
            return true;
        } else {
            return ['errors' => $model->errors];
        }
    }

    public function actionAllUsers()
    {
        $models = User::find()->where(['!=', 'role', User::ROLE_ADMIN])->all();
        //return $model;
        if ($models) {
            return User::responseAll($models, [
                'id',
                'username',
                'lat',
                'lng',
            ]);
        }

        return ['error' => 'Error. Bad request.'];
    }

    public function actionInfoUser()
    {
        return Yii::$app->user->identity->responseOne([
            'username',
            'card',
            'phone',
            'email',
            'lat',
            'lng',
        ]);
    }

    public function actionOneUser($id)
    {
        $model = User::findOne($id);

        if ($model) {
            return $model->responseOne([
                'username',
                'card',
                'phone',
                'lat',
                'lng',
            ]);
        }
        return ['error' => 'Error. Bad request.'];
    }

//$model = User::find()->where(['role' => 4])->all();
//    public function actionWed()
//    {
//        return (new Query())
//            ->select(['username',])
//            ->from('user')
//            ->leftJoin(['lat' => Coordinates::tableName()], 'lat.' )
//            ->column();
//    }

//    protected function findModel($id)
//    {
//        if (($model = User::findOne($id)) !== null) {
//            if ($model->deleted == User::STATUS_ACTIVE) {
//                return $model;
//            } else {
//                throw new NotFoundHttpException('The record was archived.');
//            }
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }

    //    public function actionCouriers(){
//        $key = '1';
//        $user = $this->check($key);
//        if ($user['lvl'] >= 2){
//
//            //
//            $worker = UserSearch::findIdentity($user['id']);
//
//            return "nice";
//        }
//        return 'bad';
//    }
//    public function actionTest(){
//        $key = '1';
//        $lvlacces = $this->check($key);
//        if ($lvlacces > 2){
//            return "nice";
//        }
//        return 'bad';
//    }
//    public function check($key){
//        $user = UserSearch::findByUsername($key);
//        $role = $user->getRoleText();
//        $lvlaccess = 0;
//        switch ($role){
//            case 'Admin': $lvlaccess = 3; break;
//            case 'Worker': $lvlaccess = 2; break;
//            case 'Courier': $lvlaccess = 1; break;
//        }
//
//        return ['lvl' => $lvlaccess, 'id' => $user->getId()];
//    }

//    public function actionLogin()
//    {
//        if (!\Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
//    }

}


