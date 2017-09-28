<?php

namespace common\models;

use common\components\helpers\ExtendedActiveRecord;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Chat extends ExtendedActiveRecord
{
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
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by'
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['status', 'user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['user_id'], 'compare', 'compareValue' => Yii::$app->user->id, 'operator' => '!=', 'message' => 'You cannot chat with you.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'room_id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    public static function allFields($result)
    {
        return self::responseAll($result, [
            'room_id',
            //'user',
        ]);
    }

    public static function roomFields($result)
    {
        return self::responseAll($result, [
            'room_id',
        ]);
    }

    public function extraFields()
    {
        return [
            'user' => function ($model) {
                if ($model->created_by === Yii::$app->user->id) {
                    $user = User::findOne($model->user_id);
                    return $user ? $user->username : '';
                }
                return $model->creator->username;
            },
        ];
    }
}
