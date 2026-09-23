<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Contrato $model */

$this->title = 'Editar contrato: ' . $model->codigo;
?>
<div class="contrato-update">

    <?= Html::a('&larr; Regresar', ['view', 'id' => $model->id], ['class' => 'btn-back']) ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'firmas' => $firmas,
    ]) ?>

</div>
