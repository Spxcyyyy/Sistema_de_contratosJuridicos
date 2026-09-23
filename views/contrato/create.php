<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Contrato $model */

$this->title = 'Nuevo contrato';
?>
<div class="contrato-create">

    <?= Html::a('&larr; Regresar', ['index'], ['class' => 'btn-back']) ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'firmas' => $firmas,
    ]) ?>

</div>
