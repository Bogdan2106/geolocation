<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Coordinates */

$this->title = 'Create Coordinates';
$this->params['breadcrumbs'][] = ['label' => 'Coordinates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coordinates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
