<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.09.2017
 * Time: 13:47
 */

namespace common\components\traits;

use Yii;

trait soft
{
    # class name

    public function load($data, $formName = null)
    {
        if (array_key_exists($this->formName(), $data)) {
            return parent::load($data, $formName);
        }

        return parent::load([$this->formName() => $data], $formName);
    }
}