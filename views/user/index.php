<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use app\models\User;

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 contrato-titulo">Usuarios</h1>
        <?= Html::a('+ Nuevo usuario', ['create'], ['class' => 'btn btn-primary px-4']) ?>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-hover mb-0 grid-view'],
            'columns' => [
                'username',
                'email',
                [
                    'attribute' => 'role',
                    'value' => function ($model) {
                        return match ($model->role) {
                            User::ROLE_ADMIN => 'Administrador',
                            User::ROLE_RECABADOR => 'Recabador',
                            default => 'Usuario',
                        };
                    },
                    'filter' => [
                        User::ROLE_ADMIN => 'Administrador',
                        User::ROLE_RECABADOR => 'Recabador',
                        User::ROLE_USUARIO => 'Usuario',
                    ],
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->status == 10
                            ? '<span class="badge-estado-ok">Activo</span>'
                            : '<span class="badge-estado-pendiente">Inactivo</span>';
                    },
                    'filter' => [10 => 'Activo', 0 => 'Inactivo'],
                ],
                [
                    'attribute' => 'reset_request',
                    'label' => 'Solicitud de contraseña',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $pending = \app\models\PasswordResetRequest::find()
                            ->where(['user_id' => $model->id, 'status' => 0])
                            ->one();

                        if ($pending) {
                            return Html::a(
                                '<span class="badge bg-warning text-dark">Pendiente</span>',
                                ['reset-password', 'id' => $model->id],
                                ['title' => 'Restablecer contraseña']
                            );
                        }
                        return '';
                    },
                ],
                [
                    'class' => ActionColumn::className(),
                    'template' => '{view} {update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>