<?php
use yii\helpers\Html;
$compacto = $compacto ?? false;
?>
<article class="actividad-item">
    <span class="actividad-dot" aria-hidden="true"></span>
    <div class="actividad-body">
        <div class="actividad-heading"><strong><?= Html::encode($model->descripcion) ?></strong><time datetime="<?= date('c', $model->created_at) ?>"><?= Html::encode(Yii::$app->formatter->asDatetime($model->created_at)) ?></time></div>
        <p class="actividad-meta"><?= Html::encode($model->actor_nombre) ?> · <?= $model->contrato_id ? Html::a(Html::encode($model->codigo), ['/contrato/view', 'id' => $model->contrato_id]) : Html::encode($model->codigo) . ' (eliminado)' ?></p>
        <?php if (!$compacto): ?>
            <details class="actividad-details"><summary>Ver cambios</summary>
                <dl class="actividad-cambios">
                    <?php foreach ($model->detalleCambios as $campo => $cambio): ?>
                        <div><dt><?= Html::encode($cambio['etiqueta']) ?></dt><dd><span><?= Html::encode($cambio['antes'] === null || $cambio['antes'] === '' ? 'Sin valor' : (string) $cambio['antes']) ?></span> <span aria-label="cambió a">→</span> <strong><?= Html::encode($cambio['despues'] === null || $cambio['despues'] === '' ? 'Sin valor' : (string) $cambio['despues']) ?></strong></dd></div>
                    <?php endforeach; ?>
                </dl>
            </details>
        <?php endif; ?>
    </div>
</article>
