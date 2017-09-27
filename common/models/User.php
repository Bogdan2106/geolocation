<?php

namespace common\models;

use common\components\helpers\ExtendedActiveRecord;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\rbac\Role;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property int $service_number
 * @property int $phone
 * @property int $role
 * @property int $card
 * @property string $email
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Coordinates $coordinates
 * @property Relations[] $relations
 * @property Relations[] $relations0
 * @property User[] $couriers
 */
 class User extends ExtendedActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_ADMIN = 1;
    const ROLE_WORKER = 6;
    const ROLE_COURIER = 4;

    public $password;

    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
            ],
//            'blameable' => [
//                'class' => BlameableBehavior::className(),
//                'createdByAttribute' => 'created_by',
//                'updatedByAttribute' => 'updated_by'
//            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'service_number', 'role', 'password'], 'required'],
            [['service_number', 'phone', 'role', 'card', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['password'], 'required', 'on' => 'signup'],
            [['password'], 'string', 'min' => 2],
            [['username'],  'unique'],
            [['service_number'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'service_number' => 'Service Number',
            'phone' => 'Phone',
            'role' => 'Role',
            'card' => 'Card',
            'email' => 'Email',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

     public function oneFields()
     {
         return $this->responseOne([
             'id',
             'username',
             'auth_key',
             'phone',
             'role',
             'card',
             'lat',
             'lng',
         ]);
     }

     public static function firstFields($models)
     {
         return self::responseAll($models, [

             'username',
             'lat',
             'lng',
         ]);
     }

     public static function allFields($models)
     {
         return self::responseAll($models, [
             'id',
             'username',
             'phone',
             'role',
             'card',
             'lat',
             'lng',
         ]);
     }

     public function extraFields()
     {
         return [
             'lat' => function ($model){
                return $model->coordinates->lat;
             },
             'lng' => function ($model){
                 return $model->coordinates->lng;
             },
         ];
     }

     public function getStatusText()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'Active';
            case self::STATUS_DELETED:
                return 'Deleted';
        }
    }


    public function getRoleText()
    {
        switch ($this->role) {
            case self::ROLE_ADMIN:
                return 'Admin';
            case self::ROLE_WORKER:
                return 'Worker';
            case self::ROLE_COURIER:
                return 'Courier';
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoordinates()
    {
        return $this->hasOne(Coordinates::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelations()
    {
        return $this->hasMany(Relations::className(), ['id_courier' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelations0()
    {
        return $this->hasMany(Relations::className(), ['id_worker' => 'id']);
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }


    /* Хелперы */
    public function generateSecretKey()
    {
        $this->secret_key = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removeSecretKey()
    {
        $this->secret_key = null;
    }

    public static function isSecretKeyExpire($key)
    {
        if (empty($key)) {
            return false;
        }
        $expire = Yii::$app->params['secretKeyExpire'];
        $parts = explode('_', $key);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * @inheritdoc
     */
     public static function findIdentityByAccessToken($token, $type = null)
     {
         return static::findOne(['auth_key' => $token]);
//        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
     }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }


    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

     public function getCouriers()
     {
         return $this->hasMany(self::className(), ['id' => 'id_courier'])
             ->viaTable('relations', ['id_worker' => 'id']);
     }



//     public function getMyCouriers()
//     {
//         if ($this->role == User::ROLE_COURIER || $this->role == User::ROLE_ADMIN){
//             return $this->hasMany(User::className(), ['user_id' => 'id']);
//         }
//
//     }
}
