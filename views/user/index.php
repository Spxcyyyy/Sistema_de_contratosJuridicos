<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use app\components\ListReturnUrl;
use app\models\User;

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
$listado = Yii::$app->request->getQueryString();
?>
<div class="user-index">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="m-0 contrato-titulo">Usuarios</h1>
        <div class="d-flex flex-wrap gap-2">
            <?= Html::a('Limpiar filtros', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('+ Nuevo usuario', ['create'], ['class' => 'btn btn-success px-4']) ?>
        </div>
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
                        return $model->status == User::STATUS_ACTIVE
                            ? '<span class="badge-estado-ok">Activo</span>'
                            : '<span class="badge-estado-inactivo">Desactivado</span>';
                    },
                    'filter' => [10 => 'Activo', 0 => 'Desactivado'],
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
                    'urlCreator' => static function ($action, $model) use ($listado) {
                        $route = [$action, 'id' => $model->id];
                        if ($listado !== '' && in_array($action, ['view', 'update'], true)) {
                            $route[ListReturnUrl::PARAM] = $listado;
                        }
                        return Url::to($route);
                    },
                    'template' => '{view} {update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
