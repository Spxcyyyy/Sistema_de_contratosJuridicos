<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 contrato-titulo"><?= Html::encode($model->username) ?></h1>
        <div>
            <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-guinda']) ?>
            <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => '¿Seguro que deseas eliminar este usuario?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

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
                    'value' => $model->status == 10 ? 'Activo' : 'Inactivo',
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                ],
            ],
        ]) ?>
    </div>
</div>