<?php

use app\models\Firma;
use yii\helpers\Html;
use app\components\AccessPolicy;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\searchs\FirmaSearchs $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Firmas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firma-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (AccessPolicy::allows('firma/create')): ?>
            <?= Html::a('Create Firma', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'contrato_id',
            'nombre',
            'created_at',
            [
                'class' => ActionColumn::className(),
                'visibleButtons' => [
                    'view' => AccessPolicy::allows('firma/view'),
                    'update' => AccessPolicy::allows('firma/update'),
                    'delete' => AccessPolicy::allows('firma/delete'),
                ],
                'urlCreator' => function ($action, Firma $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
