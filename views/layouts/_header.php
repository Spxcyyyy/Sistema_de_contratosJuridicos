<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;
use app\components\AccessPolicy;

$items = [
    ['label' => 'Inicio', 'url' => ['/site/index'], 'visible' => Yii::$app->user->isGuest || !Yii::$app->user->identity->isRecabador()],
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

$brandLabel = '<svg class="brand-seal" width="24" height="24" viewBox="0 0 24 24" fill="none"'
    . ' xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
    . '<circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="1.4"/>'
    . '<circle cx="12" cy="12" r="7.6" stroke="currentColor" stroke-width="1" stroke-dasharray="1.4 2"/>'
    . '<path d="M9 12.6l2 2 4.3-4.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>'
    . '</svg><span class="brand-text">'
    . '<span class="brand-org">SEDUZAC</span>'
    . '<span class="brand-app">' . Html::encode(Yii::$app->params['appShortName']) . '</span>'
    . '</span>';
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
