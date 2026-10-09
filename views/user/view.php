<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;
use app\components\ListReturnUrl;

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ListReturnUrl::url('user/index')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <?= Html::a('&larr; Regresar', ListReturnUrl::url('user/index'), ['class' => 'btn-back']) ?>
    <h1 class="contrato-titulo mb-4"><?= Html::encode($model->username) ?></h1>

    <div class="form-card">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table detail-view-table mb-0'],
            'attributes' => [
                'username',
                'email',
                [
                    'attribute' => 'role',
                    'value' => match ($model->role) { 'admin' => 'Administrador', 'recabador' => 'Recabador', default => 'Usuario' },
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => $model->status == User::STATUS_ACTIVE
                        ? '<span class="badge-estado-ok">Activo</span>'
                        : '<span class="badge-estado-inactivo">Desactivado</span>',
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                ],
            ],
        ]) ?>
    </div>
</div>
