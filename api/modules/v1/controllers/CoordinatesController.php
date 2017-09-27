<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class CoordinatesController extends ActiveController
{
    public $modelClass = 'common\models\Coordinates';


    public function actionTest()
    {
        echo 'Test coordinates'; exit;
    }
}


