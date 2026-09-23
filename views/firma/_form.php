<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Firma $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="firma-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'contrato_id')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true, 'data-letters-only' => 'true'])->hint('Solo letras y espacios, máximo 50 caracteres.') ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => 50]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
