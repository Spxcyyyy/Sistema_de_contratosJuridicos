<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\ListReturnUrl;

/** @var yii\web\View $this */
/** @var app\models\Firma $model */

$this->title = 'Firma de ' . $model->nombre;
$volver = ListReturnUrl::url('firma/index', $listado);
$this->params['breadcrumbs'][] = ['label' => 'Firmas', 'url' => $volver];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firma-view">

    <?= Html::a('&larr; Regresar', $volver, ['class' => 'btn-back']) ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ver contrato', ['/contrato/view', 'id' => $model->contrato_id], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Código del contrato',
                'value' => $model->contrato?->codigo ?? '—',
            ],
            [
                'label' => 'Nomenclatura',
                'value' => $model->contrato?->nomenclatura ?? '—',
            ],
            'nombre',
            [
                'label' => 'Estado',
                'format' => 'raw',
                'value' => $model->estado === 'firmado'
                    ? '<span class="badge-estado-ok">Firmado</span>'
                    : '<span class="badge-estado-pendiente">Pendiente</span>',
            ],
            [
                'label' => 'Fecha de firma',
                'value' => $model->fecha_firma
                    ? Yii::$app->formatter->asDatetime($model->fecha_firma, 'php:d/m/Y H:i')
                    : 'Sin registrar',
            ],
            [
                'label' => 'Fecha de registro',
                'value' => Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i'),
            ],
        ],
    ]) ?>

</div>
