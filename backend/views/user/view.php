<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'service_number',
            'phone',
            [
                'attribute' => 'role',
                'format' => 'text',
                'value' => function ($data) {
                    return $data->RoleText;
                },
            ],
            'card',
            'email:email',
//            'auth_key',
//            'password_hash',
//            'password_reset_token',
            [
                'attribute' => 'status',
                'format' => 'text',
                'value' => function ($data) {
                    return $data->StatusText;
                },
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
