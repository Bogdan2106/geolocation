<?php

namespace common\components\helpers;

use common\models\User;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
//use yii\rest\Serializer;

/**
 * Class ExtendedActiveRecord
 * @package common\components\helpers
 *
 * @property string $className
 */
class ExtendedActiveRecord extends ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    const FIELD_NAME = 'status';

    /**
     * @param $dataProvider
     * @param array $fields
     * @return array
     */
    public static function responseAll($dataProvider, $fields = [])
    {
        if ($dataProvider instanceof DataProviderInterface) {
            return [
                'models' => self::getFields($dataProvider->getModels(), $fields),
                'current_page' => $dataProvider->pagination->page,
                'count_page' => $dataProvider->pagination->pageCount,
                'count_model' => $dataProvider->getTotalCount()
            ];
        }

        return self::getFields($dataProvider, $fields);
    }

    /**
     * @param array $fields
     * @return array
     */
    public function responseOne($fields = [])
    {
        return [
            strtolower($this->formName()) => self::getFields($this, $fields),
        ];
    }

    /**
     * The default implementation returns the fields defined in the fields method
     *
     * @param $result
     * @return array
     */
    public static function allFields($result)
    {
        return self::responseAll($result, []);
    }

    /**
     * The default implementation returns the fields defined in the fields method
     *
     * @return array
     */
    public function oneFields()
    {
        return $this->responseOne([]);
    }

    /**
     * Get a list of models with certain fields.
     *
     * If you do not pass attributes to the second parameter, or pass an empty array,
     * all the fields described in the fields() method will be returned.
     * If the output fields are passed and the handler function or a string
     * with the name of another field ['user' => 'Username'] will not be passed to the specified attribute,
     * the description will be used from the fields() method or extraFields() (if any)
     *
     * @param $models
     * @param array $attributes
     * @return array
     */
    public static function getFields($models, array $attributes = [])
    {
        if (!is_array($models)) {
            $models = [$models];
        }

        if (!count($models)) {
            return [];
        }

        if (count($attributes) && method_exists($models[0], 'fields')) {
            $fields = ArrayHelper::merge($models[0]->fields(), $models[0]->extraFields());
            $attr   = [];

            foreach ($attributes as $key => $val) {
                if (is_int($key) && array_key_exists($val, $fields)) {
                    $attr[$val] = $fields[$val];
                } else {
                    if (is_int($key)) {
                        $attr[] = $val;
                    }
                    else {
                        $attr[$key] = $val;
                    }
                }
            }

            return ArrayHelper::toArray(
                $models, [self::className() => $attr]
            );
        }

        return ArrayHelper::toArray($models);

        // $serializer = new Serializer;
        // return array_map(function ($model) use ($serializer) {
        //     return $serializer->serialize($model);
        // }, $models);
    }

    /**
     * This method set 'status' = STATUS_DELETED
     * @param bool $softDelete
     * @return bool|array
     * @throws \Exception
     */
    public function delete($softDelete = true)
    {
        if ($softDelete) {// && self::STATUS_ACTIVE
            if ($this->beforeDelete()) {
                $name = self::FIELD_NAME;
                if ($this->$name !== self::STATUS_DELETED) {
                    $this->setStatus(self::STATUS_DELETED);

                    if ($this->save()) {
                        $this->afterDelete();
                        return true;
                    }
                    return $this->errors;
                }
                return true;
            }
            return false;
        }

        return parent::delete();
    }

    /**
     * @param $newStatus
     * @return mixed
     */
    public function setStatus($newStatus)
    {
        $name = self::FIELD_NAME;
        return $this->$name = $newStatus;
    }

    /**
     * @return bool||string
     */
    public function errors()
    {
        foreach ($this->getErrors() as $error) {
            return is_string($error) ? $error : $error[0];
        }
        return false;
    }

    /**
     * Alias for formName()
     * @return string
     */
    public function getClassName()
    {
        return $this->formName();
        // $namespace = get_class($this);
        // $name = explode("\\", $namespace);
        // return end($name);
    }

    /**
     * Get the user object by id in created_by
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
