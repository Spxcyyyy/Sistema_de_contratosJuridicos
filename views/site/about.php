<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Acerca de';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Acerca del ' . Yii::$app->params['appName'] . '.';
?>
<div class="site-about d-flex align-items-center justify-content-center text-center">
    <div class="site-about-content mx-auto">
        <h1 class="display-6 fw-semibold mb-3"><?= Html::encode(Yii::$app->params['appShortName']) ?></h1>

        <p class="text-body-secondary mb-4">
            <?= Html::encode(Yii::$app->params['appName']) ?>. Permite registrar contratos,
            dar seguimiento a las firmas requeridas y consultar su estado en cualquier momento.
        </p>

        <?= Html::a(
            'Ir al inicio',
            Yii::$app->homeUrl,
            ['class' => 'btn btn-outline-primary btn-lg'],
        ) ?>
    </div>
</div>
