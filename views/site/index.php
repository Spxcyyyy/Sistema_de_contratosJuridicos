<?php

/** @var yii\web\View $this */
/** @var array|null $stats */

use yii\helpers\Html;
use app\components\AccessPolicy;

$this->title = Yii::$app->params['appShortName'];
$this->params['meta_description'] = Yii::$app->params['appName'] . '. Consulta, registro y seguimiento de firmas de contratos.';
?>
<div class="site-index">

    <!-- Panel de bienvenida -->
    <div class="hero-banner text-white rounded-4 p-5 mb-4 position-relative overflow-hidden">
        <div class="position-relative">
            <h1 class="display-6 fw-bold mb-3"><?= Html::encode(Yii::$app->params['appName']) ?></h1>
            <p class="lead opacity-75 mb-4 hero-lead">
                Registra, consulta y da seguimiento al estado de firmas de los contratos
                de la dependencia desde un solo lugar.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <?php if (Yii::$app->user->isGuest): ?>
                    <?= Html::a('Iniciar sesión', ['/site/login'], ['class' => 'btn btn-light btn-lg fw-semibold px-4']) ?>
                <?php else: ?>
                    <?php if (AccessPolicy::allows('contrato/index')): ?>
                        <?= Html::a('Ver contratos', ['/contrato/index'], ['class' => 'btn btn-light btn-lg fw-semibold px-4']) ?>
                    <?php endif; ?>
                    <?php if (AccessPolicy::allows('contrato/create')): ?>
                        <?= Html::a('+ Nuevo contrato', ['/contrato/create'], ['class' => 'btn btn-outline-light btn-lg px-4']) ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($stats !== null): ?>
        <!-- Estadísticas -->
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="stat-card stat-guinda">
                    <div class="stat-value"><?= $stats['total'] ?></div>
                    <div class="stat-label">Contratos totales</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card stat-pendiente">
                    <div class="stat-value"><?= $stats['pendientes'] ?></div>
                    <div class="stat-label">En proceso de firmas</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card stat-ok">
                    <div class="stat-value"><?= $stats['completados'] ?></div>
                    <div class="stat-label">Firmas completadas</div>
                </div>
            </div>
        </div>

        <!-- Accesos directos -->
        <div class="row g-3">
            <div class="col-md-4">
                <?= Html::a(
                    '<div class="card h-100 border-0 shadow-sm rounded-3 extension-card quick-access-card">'
                        . '<div class="card-body">'
                            . '<div class="quick-access-icon mb-2" aria-hidden="true">&#128196;</div>'
                            . '<h3 class="h6 fw-bold mb-1">Contratos</h3>'
                            . '<p class="text-body-secondary small mb-0">Consulta el listado completo y filtra por estado.</p>'
                        . '</div>'
                    . '</div>',
                    ['/contrato/index'],
                    [],
                ) ?>
            </div>
            <div class="col-md-4">
                <?= Html::a(
                    '<div class="card h-100 border-0 shadow-sm rounded-3 extension-card quick-access-card">'
                        . '<div class="card-body">'
                            . '<div class="quick-access-icon mb-2" aria-hidden="true">&#128203;</div>'
                            . '<h3 class="h6 fw-bold mb-1">Generar reporte</h3>'
                            . '<p class="text-body-secondary small mb-0">Exporta un reporte de contratos y su estado de firmas.</p>'
                        . '</div>'
                    . '</div>',
                    ['/contrato/reporte'],
                    [],
                ) ?>
            </div>
            <div class="col-md-4">
                <?php if (AccessPolicy::allows('user/index')): ?>
                    <?= Html::a(
                        '<div class="card h-100 border-0 shadow-sm rounded-3 extension-card quick-access-card">'
                            . '<div class="card-body">'
                                . '<div class="quick-access-icon mb-2" aria-hidden="true">&#128101;</div>'
                                . '<h3 class="h6 fw-bold mb-1">Usuarios</h3>'
                                . '<p class="text-body-secondary small mb-0">Administra las cuentas y roles del sistema.</p>'
                            . '</div>'
                        . '</div>',
                        ['/user/index'],
                        [],
                    ) ?>
                <?php elseif (AccessPolicy::allows('contrato/create')): ?>
                    <?= Html::a(
                        '<div class="card h-100 border-0 shadow-sm rounded-3 extension-card quick-access-card">'
                            . '<div class="card-body">'
                                . '<div class="quick-access-icon mb-2" aria-hidden="true">&#10133;</div>'
                                . '<h3 class="h6 fw-bold mb-1">Nuevo contrato</h3>'
                                . '<p class="text-body-secondary small mb-0">Registra un nuevo contrato en el sistema.</p>'
                            . '</div>'
                        . '</div>',
                        ['/contrato/create'],
                        [],
                    ) ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
