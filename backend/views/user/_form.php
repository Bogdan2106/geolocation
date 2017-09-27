<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'service_number')->textInput() ?>

    <?= $form->field($model, 'phone')->textInput() ?>

    <?= $form->field($model, 'role')->dropDownList([
        \common\models\User::ROLE_WORKER  => 'Worker',
        \common\models\User::ROLE_COURIER  => 'Courier',
        \common\models\User::ROLE_ADMIN  => 'Admin',
    ])
    ?>

    <?= $form->field($model, 'card')->textInput() ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?//= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?//= $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>

<!--    --><?//= $form->field($model, 'status')->dropDownList([
//        \common\models\User::STATUS_ACTIVE => 'Active',
//        \common\models\User::STATUS_DELETED => 'Deleted',
//    ])
//    ?>

<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
