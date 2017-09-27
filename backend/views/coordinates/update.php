<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Coordinates */

$this->title = 'Update Coordinates: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Coordinates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_user, 'url' => ['view', 'id' => $model->id_user]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coordinates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
