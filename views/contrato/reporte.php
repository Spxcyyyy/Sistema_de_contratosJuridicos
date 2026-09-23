<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\models\ReporteContratoForm;

$this->title = 'Generar reporte';
$this->registerCssFile('@web/css/contrato.css');
$porSeleccion = $reporte->alcance === 'seleccion';
?>
<div class="contrato-reporte">
    <?= Html::a('&larr; Regresar a contratos', ['index'], ['class' => 'btn-back']) ?>
    <h1 class="contrato-titulo mb-4">Generar reporte</h1>
    <div class="form-card">
        <?php $form = ActiveForm::begin(['method' => 'post', 'action' => ['reporte']]); ?>
        <?= Html::errorSummary($reporte, ['class' => 'alert alert-danger', 'role' => 'alert']) ?>
        <?= Html::hiddenInput('alcance', is_string($reporte->alcance) ? $reporte->alcance : '') ?>
        <?= Html::hiddenInput('ids_json', is_string($reporte->ids_json) ? $reporte->ids_json : '') ?>
        <?php if ($porSeleccion): ?>
            <h2 class="form-card-title">Contratos seleccionados (<?= count($seleccionados) ?>)</h2>
            <p class="text-muted">El reporte incluirá únicamente estos contratos, sin filtrar por fecha.</p>
            <?php if ($seleccionados): ?>
                <details class="mb-3" open>
                    <summary class="mb-2">Revisar selección</summary>
                    <div class="d-flex flex-wrap gap-2" style="max-height: 220px; overflow-y: auto;">
                        <?php foreach ($seleccionados as $contrato): ?>
                            <span class="badge text-bg-light border p-2"><?= Html::encode($contrato['codigo']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php endif; ?>
            <?= Html::a('Cambiar selección', ['index'], ['class' => 'btn btn-sm btn-outline-guinda']) ?>
        <?php else: ?>
            <h2 class="form-card-title">Rango de fechas (documento)</h2>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label for="reporte-desde" class="form-label">Desde</label><?= Html::input('date', 'desde', is_string($reporte->desde) ? $reporte->desde : '', ['id' => 'reporte-desde', 'class' => 'form-control']) ?></div>
                <div class="col-md-6"><label for="reporte-hasta" class="form-label">Hasta</label><?= Html::input('date', 'hasta', is_string($reporte->hasta) ? $reporte->hasta : '', ['id' => 'reporte-hasta', 'class' => 'form-control']) ?></div>
            </div>
            <p class="text-muted small">Deja las fechas vacías para incluir todos los contratos. Para elegir contratos específicos, selecciónalos desde el listado y pulsa «Reporte de seleccionados».</p>
        <?php endif; ?>
        <hr>
        <h2 class="form-card-title">Columnas a incluir</h2>
        <div class="row g-2 mb-3">
            <?php foreach (ReporteContratoForm::COLUMNAS as $key => $label): ?>
                <div class="col-md-4"><div class="form-check">
                    <?= Html::checkbox('columnas[]', is_array($reporte->columnas) && in_array($key, $reporte->columnas, true), ['value' => $key, 'id' => 'col_' . $key, 'class' => 'form-check-input']) ?>
                    <label class="form-check-label" for="col_<?= $key ?>"><?= Html::encode($label) ?></label>
                </div></div>
            <?php endforeach; ?>
        </div>
        <hr>
        <h2 class="form-card-title">Formato</h2>
        <div class="mb-4"><label for="reporte-formato" class="visually-hidden">Formato del archivo</label>
            <?= Html::dropDownList('formato', is_string($reporte->formato) ? $reporte->formato : '', ['pdf' => 'PDF', 'xlsx' => 'Excel (.xlsx)', 'csv' => 'CSV'], ['id' => 'reporte-formato', 'class' => 'form-select', 'required' => true]) ?>
        </div>
        <div class="form-actions">
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-link text-secondary']) ?>
            <?= Html::submitButton('Descargar reporte', ['class' => 'btn btn-primary px-4']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
