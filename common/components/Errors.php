<?php

namespace common\components;


trait Errors
{
    # error

    public function errors($attribute = null)
    {
        $errors = parent::getErrors($attribute);
        return \Yii::t('app', $this->errorRecursive($errors));
    }

    protected function errorRecursive($error)
    {
        if (is_array($error)) {
            return $this->errorRecursive(array_shift($error));
        }
        return $error;
    }

    /**
     * @return bool||string
     */
//    public function errors()
//    {
//        foreach (parent::getErrors() as $error){
//            return $error[0];
//        }
//        return false;
//    }
}