<?php

use app\models\Firma;
use yii\helpers\Html;
use yii\grid\GridView;
use app\components\ListReturnUrl;

/** @var yii\web\View $this */
/** @var app\models\searchs\FirmaSearchs $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Firmas';
$this->params['breadcrumbs'][] = $this->title;
$listado = Yii::$app->request->getQueryString();
?>
<div class="firma-index">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Limpiar filtros', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <p class="text-muted">Agrega, edita o quita firmantes desde el contrato correspondiente.</p>

    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => \yii\helpers\Url::to(['index']),
        'layout' => "{summary}\n<div class=\"table-responsive\">{items}</div>\n{pager}",
        'summary' => 'Mostrando {begin}–{end} de {totalCount} firmas',
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
            'options' => ['class' => 'listado-paginacion', 'aria-label' => 'Páginas de firmas'],
            'prevPageLabel' => 'Anterior',
            'nextPageLabel' => 'Siguiente',
            'maxButtonCount' => 5,
        ],
        'tableOptions' => ['class' => 'table table-hover mb-0 grid-view'],
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
                    . Html::hiddenInput(ListReturnUrl::PARAM, $listado)
                    . Html::submitButton('Ver', [
                        'class' => 'btn btn-sm btn-outline-primary',
                        'aria-label' => 'Ver firma de ' . $model->nombre,
                    ])
                    . Html::endForm(),
            ],
        ],
    ]); ?>
    </div>

</div>
