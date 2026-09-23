<?php

use yii\helpers\Html;
use app\components\AccessPolicy;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Firma $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Firmas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="firma-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (AccessPolicy::allows('firma/update')): ?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if (AccessPolicy::allows('firma/delete')): ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'contrato_id',
            'nombre',
            'created_at',
        ],
    ]) ?>

</div>
