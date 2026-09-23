<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\searchs\ContratoSearchs $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="contrato-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'codigo')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'encargado')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'nomenclatura')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'fecha_documento') ?>

    <?php // echo $form->field($model, 'estado')->textInput(['maxlength' => 50]) ?>

    <?php // echo $form->field($model, 'costo') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
