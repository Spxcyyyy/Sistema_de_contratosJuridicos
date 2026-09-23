<?php
use yii\helpers\Html;
$this->title = 'Historial de actividad';
$this->registerCssFile('@web/css/seguimiento.css');
?>
<div class="seguimiento">
    <div class="seguimiento-heading"><div><h1>Historial de actividad</h1><p class="text-muted">Cambios registrados desde la incorporación del historial.</p></div><?= Html::a('Volver al inicio', ['/site/index'], ['class' => 'btn btn-outline-guinda']) ?></div>
    <section class="seguimiento-panel">
        <?= \yii\widgets\ListView::widget(['dataProvider' => $actividadProvider, 'itemView' => '_actividad', 'layout' => '{summary}{items}{pager}', 'emptyText' => 'Todavía no hay actividad registrada.']) ?>
    </section>
</div>
