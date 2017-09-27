<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\components\rbac\UserRoleRule;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные

        // Включаем наш обработчик
        $rule = new UserRoleRule();
        $auth->add($rule);

        //Добавляем роли
        $dashboard = $auth->createRole('admin');
        $dashboard->ruleName = $rule->name;
        $auth->add($dashboard);

        $worker = $auth->createRole('worker');
        $worker->ruleName = $rule->name;
        $auth->add($worker);

        $courier = $auth->createRole('courier');
        $courier->ruleName = $rule->name;
        $auth->add($courier);

        //Добавляем потомков
//        $auth->addChild($courier, $worker);
//        $auth->addChild($courier, $dashboard);
//
//        $admin = $auth->createRole('admin');
//        $admin->description = 'Admin';
//        $auth->add($admin);
//        $auth->addChild($admin, $worker);
    }
}