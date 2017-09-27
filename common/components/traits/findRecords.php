<?php

namespace common\components\traits;

use Yii;

trait findRecords
{
    # search records

    public function searchAll($request =  null)
    {
        $this->deleted = 0;
        if ($request && (!$this->load([soft::lastNameClass(static::className()) => $request]) || !$this->validate())) {
            return false;
        }
        $dataProvider = $this->search();
        $models = $dataProvider->getModels();
        return [
            'models' => $models,
        ];

    }
}