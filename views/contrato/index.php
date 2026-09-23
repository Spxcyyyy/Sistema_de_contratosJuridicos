<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\components\AccessPolicy;
use yii\grid\ActionColumn;

$this->registerCssFile('@web/css/contrato.css');
if (AccessPolicy::allows('contrato/reporte')) {
    $this->registerJsFile('@web/js/seleccion-reportes.js', ['depends' => [\yii\web\YiiAsset::class]]);
}

$this->title = 'Contratos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contrato-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 contrato-titulo">Contratos</h1>
        <div>
            <?php if (AccessPolicy::allows('contrato/reporte')): ?>
                <?= Html::a('Generar reporte', ['reporte'], ['class' => 'btn btn-outline-guinda me-2']) ?>
            <?php endif; ?>
            <?php if (AccessPolicy::allows('contrato/create')): ?>
                <?= Html::a('+ Nuevo contrato', ['create'], ['class' => 'btn btn-primary px-4']) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (AccessPolicy::allows('contrato/reporte')): ?>
    <div id="seleccion-reportes" data-storage-key="contratos-reporte-<?= (int) Yii::$app->user->id ?>" class="d-flex flex-wrap align-items-center gap-3 p-3 mb-3 border rounded bg-white">
        <span id="seleccion-contador" role="status" aria-live="polite">0 contratos seleccionados</span>
        <?= Html::beginForm(['reporte'], 'post', ['id' => 'reporte-seleccion-form', 'class' => 'm-0']) ?>
            <?= Html::hiddenInput('alcance', 'seleccion') ?>
            <?= Html::hiddenInput('paso', 'preparar') ?>
            <?= Html::hiddenInput('ids_json', '[]', ['id' => 'reporte-seleccion-ids']) ?>
            <?= Html::submitButton('Reporte de seleccionados', ['id' => 'reporte-seleccion-boton', 'class' => 'btn btn-primary', 'disabled' => true]) ?>
        <?= Html::endForm() ?>
        <button type="button" id="seleccion-limpiar" class="btn btn-link text-secondary" disabled>Limpiar selección</button>
        <small class="text-muted w-100" id="seleccion-ayuda">Selecciona hasta 500 contratos. La selección se conserva en esta pestaña al cambiar de página o filtrar.</small>
        <noscript><span>Activa JavaScript para seleccionar contratos. El reporte por fechas sigue disponible.</span></noscript>
    </div>
    <?php endif; ?>
    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <div class="table-responsive">
        <?= GridView::widget([
            'id' => 'contratos-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterUrl' => \yii\helpers\Url::to(['index']),
            'tableOptions' => ['class' => 'table table-hover mb-0 grid-view'],
            'columns' => [
                [
                    'class' => \yii\grid\CheckboxColumn::class,
                    'visible' => AccessPolicy::allows('contrato/reporte'),
                    'name' => 'seleccionContrato[]',
                    'header' => Html::checkbox('seleccionar-pagina', false, ['id' => 'seleccionar-pagina', 'aria-label' => 'Seleccionar contratos de esta página']),
                    'checkboxOptions' => fn($model) => ['value' => $model->id, 'class' => 'contrato-seleccion', 'aria-label' => 'Seleccionar ' . $model->codigo],
                ],
                [
                    'attribute' => 'codigo',
                    'format' => 'raw',
                    'value' => fn($model) => '<span class="contrato-codigo">' . Html::encode($model->codigo) . '</span>',
                ],
                'encargado',
                'nomenclatura',
                [
                    'attribute' => 'fecha_vencimiento',
                    'filter' => Html::activeInput('date', $searchModel, 'fecha_vencimiento', ['class' => 'form-control', 'aria-label' => 'Filtrar por fecha límite']),
                    'format' => 'raw',
                    'value' => function ($model) {
                        $texto = $model->fecha_vencimiento ? Html::encode(Yii::$app->formatter->asDate($model->fecha_vencimiento)) : 'Sin fecha límite';
                        $aviso = $model->avisoVencimiento;
                        return $texto . ($aviso ? '<br><span class="badge text-bg-' . $aviso['clase'] . '">' . Html::encode($aviso['texto']) . '</span>' : '');
                    },
                ],
                [
                    'attribute' => 'fecha_documento',
                    'filter' => Html::activeInput('date', $searchModel, 'fecha_documento', ['class' => 'form-control', 'aria-label' => 'Filtrar por fecha del documento']),
                    'format' => ['date', 'php:d/M/Y'],
                ],
                [
                    'attribute' => 'estado',
                    'filter' => Html::activeDropDownList($searchModel, 'estado', \app\models\searchs\ContratoSearchs::estadosDisponibles(), ['prompt' => 'Todos', 'class' => 'form-select', 'aria-label' => 'Filtrar por estado']),
                    'format' => 'raw',
                    'value' => function ($model) {
                        $esFirmado = in_array($model->estado, \app\models\Contrato::ESTADOS_CONCLUIDOS, true);
                        $clase = $esFirmado ? 'badge-estado-ok' : 'badge-estado-pendiente';
                        return '<span class="' . $clase . '">' . Html::encode($model->estado) . '</span>';
                    },
                ],
                [
                    'class' => ActionColumn::className(),
                    'visibleButtons' => [
                        'view' => AccessPolicy::allows('contrato/view'),
                        'update' => AccessPolicy::allows('contrato/update'),
                        'delete' => AccessPolicy::allows('contrato/delete'),
                    ],
                    'template' => '{view} {update} {delete}',
                ],
            ],
        ]); ?>
        </div>
    </div>
</div>
