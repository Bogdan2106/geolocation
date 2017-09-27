<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $service_number;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            [['service_number'], 'integer'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        if (count(User::findAll(['role' => User::ROLE_ADMIN]))) {
            $this->addError('username', 'admin uzhe est');
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->service_number = $this->service_number;
        $user->role = User::ROLE_ADMIN;

        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
//    public function signup()
//    {
//        if (!$this->validate()) {
//            return null;
//        }
//
//        /**
//         * SQL: SELECT * FROM 'user' ;
//         * Вытаскиваем всех пользователей с базы
//         */
//        $userExists = User::find()->all();
//
//
//        $user = new User();
//        $user->username = $this->username;
//        $user->email = $this->email;
//
//        /** если в базе есть пользователи - присваиваем роль обычного пользователя, если нет - админа  */
//        $user->role = $userExists ? User::ROLE_WORKER : User::ROLE_ADMIN;
//
//        //$user->status = $this->status;
//        $user->setPassword($this->password);
//        $user->generateAuthKey();
//        //    if ($this->scenario === 'emailActivation')
//       // $user->generateSecretKey(); // генерация секретного ключа
//        $user->status = User::STATUS_ACTIVE;
//
//        $isSaved = $user->save() ? $user : null;
//
////        if ($isSaved != null){
////            $this->sendEmail($isSaved);
////        }
//        return $isSaved;
//    }
}
