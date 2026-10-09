<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;
use app\components\AccessPolicy;

$items = [
    ['label' => 'Inicio', 'url' => ['/site/dashboard'], 'visible' => !Yii::$app->user->isGuest && !Yii::$app->user->identity->isRecabador()],
];

if (AccessPolicy::allows('contrato/index')) {
    $items[] = ['label' => 'Contratos', 'url' => ['/contrato/index']];
}
if (AccessPolicy::allows('firma/index')) {
    $items[] = ['label' => 'Firmas', 'url' => ['/firma/index']];
}
if (AccessPolicy::allows('user/index')) {
    $items[] = ['label' => 'Usuarios', 'url' => ['/user/index']];
}

$brandLabel = Html::img(Yii::getAlias('@web/images/logo-2021-2027.png'), [
    'class' => 'gov-brand-logo',
    'alt' => 'Secretaría de Educación del Estado de Zacatecas — ' . Yii::$app->params['appShortName'],
]);
?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => $brandLabel,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top gov-navbar']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?php if (Yii::$app->user->isGuest): ?>
        <?= Html::a('Iniciar sesión', ['/site/login'], ['class' => 'btn btn-outline-light btn-sm']) ?>
    <?php else: ?>
        <div class="navbar-user d-flex align-items-center gap-2">
            <span class="navbar-user-name"><?= Html::encode(Yii::$app->user->identity->username) ?></span>
            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline']) ?>
                <?= Html::submitButton('Salir', ['class' => 'btn btn-outline-light btn-sm']) ?>
            <?= Html::endForm() ?>
        </div>
    <?php endif; ?>
    <?php NavBar::end() ?>
</header>
