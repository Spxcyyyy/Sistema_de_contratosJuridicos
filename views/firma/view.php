<?php

use app\components\AccessPolicy;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Firma $model */

$this->title = 'Firma de ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Firmas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="firma-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ver contrato', ['/contrato/view', 'id' => $model->contrato_id], ['class' => 'btn btn-outline-secondary']) ?>
        <?php if (AccessPolicy::allows('contrato/update')): ?>
            <?= Html::a('Editar firmantes en el contrato', ['/contrato/update', 'id' => $model->contrato_id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
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
