<?php
namespace common\components\rbac;
use Yii;
use yii\rbac\Rule;
use yii\helpers\ArrayHelper;
use common\models\User;
class UserRoleRule extends Rule
{
    public $name = 'userRole';
    public function execute($user, $item, $params)
    {
        //Получаем массив пользователя из базы
//        $user = ArrayHelper::getValue($params, 'user', Yii::$app->user->identity);
        $user = ArrayHelper::getValue($params, 'user', User::findOne($user));
        // Yii::$app->user->id == $user ? ... : User::findOne($user)
        if ($user) {
            $role = $user->role;
            if ($item->name === 'admin') {
                return $role == User::ROLE_ADMIN;
            } elseif ($item->name === 'worker') {
                return $role == User::ROLE_WORKER;
            }
            elseif ($item->name === 'courier'){
                return $role == User::ROLE_COURIER;
            }
        }
        return false;
    }
}