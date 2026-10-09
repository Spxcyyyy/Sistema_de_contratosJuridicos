<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\components\AccessPolicy;
use app\components\ListReturnUrl;
use yii\grid\ActionColumn;
use yii\helpers\Url;

$this->registerCssFile('@web/css/contrato.css');
if (AccessPolicy::allows('contrato/delete')) {
    \yii\bootstrap5\BootstrapPluginAsset::register($this);
}
if (AccessPolicy::allows('contrato/reporte')) {
    $this->registerJsFile('@web/js/seleccion-reportes.js', ['depends' => [\yii\web\YiiAsset::class]]);
}

$this->title = 'Contratos';
$this->params['breadcrumbs'][] = $this->title;
$iconoEliminar = (new ActionColumn(['template' => '']))->icons['trash'];
$listado = Yii::$app->request->getQueryString();
?>
<div class="contrato-index">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="m-0 contrato-titulo">Contratos</h1>
        <div class="d-flex flex-wrap gap-2">
            <?= Html::a('Limpiar filtros', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?php if (AccessPolicy::allows('contrato/reporte')): ?>
                <?= Html::a('Generar reporte', ['reporte'], ['class' => 'btn btn-outline-primary']) ?>
            <?php endif; ?>
            <?php if (AccessPolicy::allows('contrato/create')): ?>
                <?= Html::a('+ Nuevo contrato', ['create'], ['class' => 'btn btn-success px-4']) ?>
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
    <?= $this->render('//layouts/_active_filters', [
        'searchModel' => $searchModel,
        'filters' => [
            'codigo' => 'Código',
            'encargado' => 'Encargado',
            'nomenclatura' => 'Nomenclatura',
            'fecha_vencimiento' => 'Fecha límite',
            'fecha_documento' => 'Fecha del documento',
            'estado' => 'Estado',
            'grupo' => 'Grupo del panel',
            'fecha_documento_desde' => 'Documento desde',
            'fecha_documento_hasta' => 'Documento hasta',
            'created_at_desde' => 'Creado desde',
            'created_at_hasta' => 'Creado hasta',
        ],
    ]) ?>
    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <?= GridView::widget([
            'id' => 'contratos-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterUrl' => \yii\helpers\Url::to(['index']),
            'layout' => "{summary}\n<div class=\"table-responsive\">{items}</div>\n{pager}",
            'summary' => 'Mostrando {begin}–{end} de {totalCount} contratos',
            'pager' => [
                'class' => \yii\bootstrap5\LinkPager::class,
                'options' => ['class' => 'listado-paginacion', 'aria-label' => 'Páginas de contratos'],
                'prevPageLabel' => 'Anterior',
                'nextPageLabel' => 'Siguiente',
                'maxButtonCount' => 5,
            ],
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
                    'format' => ['date', 'php:d/m/Y'],
                ],
                [
                    'attribute' => 'estado',
                    'filter' => Html::activeDropDownList($searchModel, 'estado', \app\models\searchs\ContratoSearchs::estadosDisponibles(), ['prompt' => 'Todos', 'class' => 'form-select', 'aria-label' => 'Filtrar por estado']),
                    'format' => 'raw',
                    'value' => function ($model) {
                        $esFirmado = in_array($model->estado, \app\models\Contrato::ESTADOS_CONCLUIDOS, true);
                        $clase = $esFirmado ? 'badge-estado-ok' : ($model->estado === 'Cancelado' ? 'badge-estado-inactivo' : 'badge-estado-pendiente');
                        return '<span class="' . $clase . '">' . Html::encode($model->estado) . '</span>';
                    },
                ],
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => static function ($action, $model) use ($listado) {
                        $route = [$action, 'id' => $model->id];
                        if ($listado !== '' && in_array($action, ['view', 'update'], true)) {
                            $route[ListReturnUrl::PARAM] = $listado;
                        }
                        return Url::to($route);
                    },
                    'visibleButtons' => [
                        'view' => AccessPolicy::allows('contrato/view'),
                        'update' => AccessPolicy::allows('contrato/update'),
                        'delete' => AccessPolicy::allows('contrato/delete'),
                    ],
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'delete' => static fn($url, $model) => Html::a($iconoEliminar, '#confirmar-eliminar-contrato', [
                            'class' => 'grid-delete-action',
                            'title' => 'Eliminar',
                            'data-bs-toggle' => 'modal',
                            'data-bs-target' => '#confirmar-eliminar-contrato',
                            'data-url' => $url,
                            'data-codigo' => $model->codigo,
                            'data-pjax' => '0',
                            'aria-label' => 'Eliminar contrato ' . $model->codigo,
                        ]),
                    ],
                ],
            ],
        ]); ?>
    </div>

    <?php if (AccessPolicy::allows('contrato/delete')): ?>
    <div class="modal fade" id="confirmar-eliminar-contrato" tabindex="-1" aria-labelledby="titulo-eliminar-contrato" aria-describedby="mensaje-eliminar-contrato" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="titulo-eliminar-contrato">Eliminar contrato</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <?= Html::beginForm('#', 'post', ['id' => 'form-eliminar-contrato']) ?>
                    <div class="modal-body" id="mensaje-eliminar-contrato">
                        ¿Seguro que deseas eliminar el contrato <strong id="codigo-eliminar-contrato"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <?= Html::submitButton('Eliminar contrato', ['class' => 'btn btn-danger', 'id' => 'confirmar-eliminar-boton', 'disabled' => true]) ?>
                    </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('confirmar-eliminar-contrato');
        const formulario = document.getElementById('form-eliminar-contrato');
        const codigo = document.getElementById('codigo-eliminar-contrato');
        const confirmar = document.getElementById('confirmar-eliminar-boton');

        modal.addEventListener('show.bs.modal', function (event) {
            const boton = event.relatedTarget;
            if (!boton || !boton.dataset.url) return;
            formulario.action = boton.dataset.url;
            codigo.textContent = boton.dataset.codigo;
            confirmar.disabled = false;
        });
        modal.addEventListener('hidden.bs.modal', function () {
            formulario.action = '#';
            codigo.textContent = '';
            confirmar.disabled = true;
        });
    });
    </script>
    <?php endif; ?>
</div>
