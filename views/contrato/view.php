<?php
$this->registerCssFile('@web/css/contratoView.css');
$this->registerCssFile('@web/css/seguimiento.css');

use yii\helpers\Html;
use app\components\AccessPolicy;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Contrato $model */

$this->title = 'Contrato ' . $model->codigo;
?>
<div class="contrato-view">

    <?= Html::a('&larr; Regresar', ['index'], ['class' => 'btn-back']) ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="m-0 contrato-titulo"><?= Html::encode($model->codigo) ?></h1>
            <p class="text-muted mb-0"><?= Html::encode($model->nomenclatura) ?></p>
        </div>
        <div class="contrato-actions">
            <?php if (AccessPolicy::allows('contrato/update')): ?>
                <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-guinda']) ?>
            <?php endif; ?>
            <?php if (AccessPolicy::allows('contrato/delete')): ?>
                <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => '¿Seguro que deseas eliminar este contrato?',
                    'method' => 'post',
                ],
            ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-card mb-4">
        <h2 class="form-card-title">Datos del contrato</h2>
        <?php if ($aviso = $model->avisoVencimiento): ?>
            <div class="alert alert-<?= $aviso['clase'] ?>" role="status"><?= Html::encode($aviso['texto']) ?>. Fecha límite: <?= Html::encode(Yii::$app->formatter->asDate($model->fecha_vencimiento)) ?>.</div>
        <?php endif; ?>
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table detail-view-table mb-0'],
            'attributes' => [
                'encargado',
                'nomenclatura',
                ['attribute' => 'fecha_vencimiento', 'value' => $model->fecha_vencimiento ? Yii::$app->formatter->asDate($model->fecha_vencimiento) : 'Sin fecha límite'],
                [
                    'attribute' => 'fecha_documento',
                    'format' => ['date', 'php:d/M/Y'],
                ],
                [
                    'attribute' => 'estado',
                    'format' => 'raw',
                    'value' => function ($model) {
                    $esFirmado = strtolower($model->estado) === 'firmado' || $model->estado === 'Todas las firmas recabadas';
                    $clase = $esFirmado ? 'badge-estado-ok' : 'badge-estado-pendiente';
                    return '<span id="contrato-estado-badge" class="' . $clase . '">' . Html::encode($model->estado) . '</span>';
                },
                ],
                [
                    'attribute' => 'costo',
                    'format' => ['currency', 'MXN'],
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Hora de creación',
                    'format' => 'datetime',
                ],
            ],
        ]) ?>
    </div>

    <div class="form-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="form-card-title mb-0">Firmantes</h2>
            <span class="firmas-contador">
                <?= count(array_filter($model->firmas, fn($f) => $f->estado === 'firmado')) ?>
                / <?= count($model->firmas) ?> firmados
            </span>
        </div>

        <?php if ($model->firmas): ?>
            <table class="table firmas-table mb-0" id="tabla-firmas">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Fecha de firma</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->firmas as $firma): ?>
                        <tr data-firma-id="<?= $firma->id ?>">
                            <td><?= Html::encode($firma->nombre) ?></td>
                            <td class="estado-cell">
                                <?= $firma->estado === 'firmado'
                                    ? '<span class="badge-estado-ok">Firmado</span>'
                                    : '<span class="badge-estado-pendiente">Pendiente</span>' ?>
                            </td>
                            <td class="fecha-firma-cell">
                                <?= $firma->fecha_firma ? Yii::$app->formatter->asDatetime($firma->fecha_firma) : '—' ?>
                            </td>
                            <td class="accion-cell text-end">
                                <?php if ($firma->estado !== 'firmado' && AccessPolicy::allows('contrato/marcar-firmado')): ?>
                                    <button type="button" class="btn btn-sm btn-primary btn-marcar-firmado" data-url="<?= Html::encode(Url::to(['contrato/marcar-firmado', 'id' => $firma->id])) ?>">
                                        Marcar como firmado
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted mb-0">Este contrato aún no tiene firmantes.</p>
        <?php endif; ?>
    </div>

    <?php $notas = $model->getNotas()->with('autor')->all(); ?>
    <section class="contrato-notas mt-4" aria-labelledby="notas-title">
        <div class="notas-heading">
            <h2 id="notas-title" class="form-card-title mb-1">Notas del contrato <span class="notas-count"><?= count($notas) ?></span></h2>
            <p class="text-muted mb-0">Observaciones y seguimiento en un solo lugar.</p>
        </div>

        <?php if (AccessPolicy::allows('contrato/add-nota')): ?>
            <details class="nota-composer">
            <summary class="btn btn-outline-guinda">+ Nueva nota</summary>
            <form method="post" action="<?= Url::to(['contrato/add-nota', 'id' => $model->id]) ?>" class="nota-composer-form">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <label for="nota-contenido" class="form-label fw-semibold">¿Qué necesitas registrar?</label>
                <textarea id="nota-contenido" name="contenido" class="form-control" rows="4" maxlength="50" aria-describedby="nota-ayuda" placeholder="Escribe una observación o un detalle importante del contrato…"
                    required></textarea>
                <div class="nota-composer-footer">
                    <small id="nota-ayuda" class="text-muted">Máximo 50 caracteres.</small>
                    <?= Html::submitButton('Guardar nota', ['class' => 'btn btn-primary']) ?>
                </div>
            </form>
        </details>
        <?php endif; ?>

        <?php if ($notas): ?>
        <div class="notas-grid">
            <?php foreach ($notas as $nota): ?>
                <?php $autor = $nota->autor->username ?? 'Usuario'; ?>
                <article class="nota-widget">
                    <div class="nota-widget-top">
                        <span class="nota-widget-label"><span aria-hidden="true">▤</span> NOTA</span>
                        <span class="nota-widget-id">#<?= (int) $nota->id ?></span>
                    </div>
                    <p class="nota-widget-content"><?= Html::encode($nota->contenido) ?></p>
                    <footer class="nota-widget-footer">
                        <span class="nota-avatar" aria-hidden="true"><?= Html::encode(mb_strtoupper(mb_substr($autor, 0, 1, 'UTF-8'), 'UTF-8')) ?></span>
                        <div class="nota-widget-meta">
                            <span class="nota-author"><?= Html::encode($autor) ?></span>
                            <time datetime="<?= date('c', $nota->created_at) ?>"><?= Yii::$app->formatter->asDatetime($nota->created_at) ?></time>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="notas-empty">
                <span class="notas-empty-icon" aria-hidden="true">▤</span>
                <h3>Aún no hay notas</h3>
                <p>Agrega la primera observación con «Nueva nota».</p>
            </div>
        <?php endif; ?>
    </section>

    <?php if (AccessPolicy::allows('contrato/actividad') && $actividadProvider !== null): ?>
    <section class="seguimiento-panel mt-4 mb-4" aria-labelledby="historial-title">
        <div class="seguimiento-panel-heading"><h2 id="historial-title">Historial de actividad</h2></div>
        <p class="text-muted small">Cambios registrados desde la incorporación del historial.</p>
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $actividadProvider,
            'itemView' => '_actividad',
            'layout' => '{summary}{items}{pager}',
            'emptyText' => 'Aún no hay cambios registrados para este contrato.',
        ]) ?>
    </section>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-marcar-firmado').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const row = btn.closest('tr');
                btn.disabled = true;
                btn.textContent = 'Guardando...';

                fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': yii.getCsrfToken(),
                        'Accept': 'application/json',
                    },
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.querySelector('.estado-cell').innerHTML = '<span class="badge-estado-ok">Firmado</span>';
                            row.querySelector('.fecha-firma-cell').textContent = data.fecha_firma;
                            row.querySelector('.accion-cell').innerHTML = '';

                            // el contador de firmados
                            const firmados = document.querySelectorAll('#tabla-firmas .badge-estado-ok').length;
                            const total = document.querySelectorAll('#tabla-firmas tbody tr').length;
                            document.querySelector('.firmas-contador').textContent = firmados + ' / ' + total + ' firmados';

                            if (data.todas_firmadas) {
                                const badge = document.getElementById('contrato-estado-badge');
                                badge.textContent = data.contrato_estado;
                                badge.className = 'badge-estado-ok';
                                badge.id = 'contrato-estado-badge';
                            }
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error al actualizar la firma');
                            btn.disabled = false;
                            btn.textContent = 'Marcar como firmado';
                        }
                    })
                    .catch(() => {
                        alert('Error de conexión');
                        btn.disabled = false;
                        btn.textContent = 'Marcar como firmado';
                    });
            });
        });
    });
</script>
