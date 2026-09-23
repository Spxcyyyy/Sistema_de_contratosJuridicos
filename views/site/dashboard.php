<?php
use yii\helpers\Html;
use app\components\AccessPolicy;

$this->title = 'Inicio · Seguimiento de contratos';
$this->registerCssFile('@web/css/seguimiento.css');
$tarjetas = [
    'total' => ['Todos los contratos', 'Expedientes registrados', 'neutral'],
    'proceso' => ['En proceso', 'Contratos por concluir', 'guinda'],
    'firmas' => ['Firmas pendientes', 'En contratos en proceso', 'guinda'],
    'concluidos' => ['Concluidos', 'Documentos firmados', 'success'],
    'vencidos' => ['Vencidos', 'Requieren atención', 'danger'],
    'proximos' => ['Por vencer', 'Hoy y próximos 7 días', 'warning'],
];
?>
<div class="seguimiento">
    <div class="seguimiento-heading">
        <div><span class="seguimiento-eyebrow">CONTROL DE CONTRATOS</span><h1>Tu resumen de hoy</h1><p class="text-muted mb-0"><?= Html::encode(Yii::$app->formatter->asDate(time(), 'long')) ?> · Pendientes, vencimientos y actividad reciente.</p></div>
        <?php if (AccessPolicy::allows('contrato/create')): ?>
            <?= Html::a('+ Nuevo contrato', ['/contrato/create'], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    </div>
    <div class="dashboard-stats">
        <?php foreach ($tarjetas as $grupo => [$titulo, $descripcion, $color]): ?>
            <a class="dashboard-stat stat-<?= $color ?>" href="<?= \yii\helpers\Url::to(['/contrato/index', 'ContratoSearchs' => $grupo === 'total' ? [] : ['grupo' => $grupo]]) ?>">
                <span><?= Html::encode($titulo) ?></span><strong><?= (int) $stats[$grupo] ?></strong><small><?= Html::encode($descripcion) ?> <span aria-hidden="true">→</span></small>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="dashboard-columns">
        <?php foreach (['vencidos' => ['Vencidos', $vencidos], 'proximos' => ['Próximos vencimientos', $proximos]] as $grupo => [$titulo, $contratos]): ?>
            <section class="seguimiento-panel">
                <div class="seguimiento-panel-heading"><h2><?= $titulo ?></h2><?= Html::a('Ver todos →', ['/contrato/index', 'ContratoSearchs' => ['grupo' => $grupo]]) ?></div>
                <?php if (!$contratos): ?><p class="seguimiento-empty">No hay contratos en este grupo.</p><?php endif; ?>
                <?php foreach ($contratos as $contrato): ?>
                    <?php $aviso = $contrato->avisoVencimiento; ?>
                    <a class="deadline-item" href="<?= \yii\helpers\Url::to(['/contrato/view', 'id' => $contrato->id]) ?>">
                        <div><strong><?= Html::encode($contrato->codigo) ?></strong><span><?= Html::encode($contrato->nomenclatura) ?></span><small><?= Html::encode($contrato->encargado) ?></small></div>
                        <div class="deadline-date"><span class="badge text-bg-<?= $aviso['clase'] ?>"><?= Html::encode($aviso['texto']) ?></span><small><?= Html::encode(Yii::$app->formatter->asDate($contrato->fecha_vencimiento)) ?></small></div>
                    </a>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    </div>
    <section class="seguimiento-panel mt-4">
        <div class="seguimiento-panel-heading"><h2>Actividad reciente</h2><?= Html::a('Ver historial →', ['/contrato/actividad']) ?></div>
        <?php if (!$actividades): ?><p class="seguimiento-empty">Aquí aparecerán los cambios que se realicen a partir de ahora.</p><?php endif; ?>
        <?php foreach ($actividades as $actividad): ?>
            <?= $this->render('/contrato/_actividad', ['model' => $actividad, 'compacto' => true]) ?>
        <?php endforeach; ?>
    </section>
    <p class="text-muted small mt-3">Los avisos se actualizan al abrir el panel. Los contratos concluidos o cancelados no generan alertas.</p>
</div>
