<?php

use app\models\Firma;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\searchs\FirmaSearchs $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Firmas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firma-index">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Limpiar filtros', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <p class="text-muted">Agrega, edita o quita firmantes desde el contrato correspondiente.</p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'nomenclaturaContrato',
                'value' => static fn(Firma $model) => $model->contrato?->nomenclatura ?? '—',
            ],
            [
                'attribute' => 'codigoContrato',
                'value' => static fn(Firma $model) => $model->contrato?->codigo ?? '—',
            ],
            'nombre',
            [
                'attribute' => 'estado',
                'format' => 'raw',
                'value' => static fn(Firma $model) => $model->estado === 'firmado'
                    ? '<span class="badge-estado-ok">Firmado</span>'
                    : '<span class="badge-estado-pendiente">Pendiente</span>',
                'filter' => ['firmado' => 'Firmado', 'pendiente' => 'Pendiente'],
            ],
            [
                'attribute' => 'fechaRegistro',
                'value' => static fn(Firma $model) => $model->created_at,
                'format' => ['datetime', 'php:d/m/Y H:i'],
                'filter' => Html::activeInput('date', $searchModel, 'fechaRegistro', ['class' => 'form-control']),
            ],
            [
                'label' => 'Acciones',
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => static fn(Firma $model) => Html::beginForm(['/firma/seleccionar'], 'post', ['class' => 'd-inline'])
                    . Html::hiddenInput('id', $model->id)
                    . Html::submitButton('Ver', [
                        'class' => 'btn btn-sm btn-outline-primary',
                        'aria-label' => 'Ver firma de ' . $model->nombre,
                    ])
                    . Html::endForm(),
            ],
        ],
    ]); ?>

</div>
