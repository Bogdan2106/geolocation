<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "relations".
 *
 * @property int $id_worker
 * @property int $id_courier
 *
 * @property User $courier
 * @property User $worker
 */
class Relations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'relations';
    }

    public static function primaryKey()
    {
        return ['id_worker', 'id_courier'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_worker', 'id_courier'], 'integer'],
            [['id_courier'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_courier' => 'id']],
            [['id_worker'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_worker' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_worker' => 'Id Worker',
            'id_courier' => 'Id Courier',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourier()
    {
        return $this->hasOne(User::className(), ['id' => 'id_courier']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorker()
    {
        return $this->hasOne(User::className(), ['id' => 'id_worker']);
    }

}
