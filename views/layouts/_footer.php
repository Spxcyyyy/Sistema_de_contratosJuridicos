<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use app\components\AccessPolicy;

?>
<footer id="footer" class="mt-auto py-4 bg-body-tertiary gov-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-heading"><?= Html::encode(Yii::$app->params['appShortName']) ?></div>
                <p class="text-body-secondary small mb-0">
                    <?= Html::encode(Yii::$app->params['appName']) ?>.
                    Herramienta interna para el registro y seguimiento de contratos.
                </p>
            </div>
            <div class="col-md-4">
                <div class="footer-heading">Enlaces</div>
                <ul class="list-unstyled small mb-0">
                    <?php if (AccessPolicy::allows('contrato/index')): ?>
                        <li class="mb-1"><?= Html::a('Contratos', ['/contrato/index']) ?></li>
                    <?php endif; ?>
                    <?php if (AccessPolicy::allows('contrato/reporte')): ?>
                        <li class="mb-1"><?= Html::a('Generar reporte', ['/contrato/reporte']) ?></li>
                    <?php endif; ?>
                    <li class="mb-1"><?= Html::a('Acerca de', ['/site/about']) ?></li>
                    <li class="mb-1"><?= Html::a('Contacto', ['/site/contact']) ?></li>
                </ul>
            </div>
            <div class="col-md-4">
                <div class="footer-heading">Aviso</div>
                <p class="text-body-secondary small mb-0">
                    Este sistema es de uso interno. El acceso y las acciones realizadas
                    quedan sujetos a las políticas internas de manejo de información.
                </p>
            </div>
        </div>
        <hr class="my-3">
        <div class="d-flex flex-wrap justify-content-between text-body-secondary small">
            <span>&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->params['appShortName']) ?>. Todos los derechos reservados.</span>
            <span>Sitio institucional</span>
        </div>
    </div>
</footer>
