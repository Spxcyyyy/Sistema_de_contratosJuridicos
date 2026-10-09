<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Iniciar sesión';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Inicia sesión en ' . Yii::$app->params['appShortName'] . '.';
$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>
<div class="site-login d-flex align-items-center justify-content-center py-5">
    <div class="card border-0 overflow-hidden login-split-card">
        <div class="row g-0">

            <!-- Brand panel -->
            <div class="col-md-5 d-none d-md-flex login-brand-panel text-white">
                <div class="d-flex flex-column justify-content-between p-4 p-lg-5 w-100">
                    <div class="d-flex flex-column align-items-start gap-3">
                        <span class="login-logo-frame">
                            <?= Html::img(Yii::getAlias('@web/images/logo-2021-2027.png'), [
                                'class' => 'login-brand-logo',
                                'alt' => 'Secretaría de Educación del Estado de Zacatecas',
                            ]) ?>
                        </span>
                        <span class="fw-semibold"><?= Html::encode(Yii::$app->params['appShortName']) ?></span>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-3 login-brand-title">
                            Bienvenido<br>de vuelta
                        </h2>
                        <p class="opacity-75 mb-0 login-brand-text">
                            Inicia sesión para consultar y dar seguimiento a los contratos.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form panel -->
            <div class="col-md-7">
                <div class="p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
                        <p class="text-body-secondary small">Ingresa tus credenciales para continuar</p>
                    </div>

                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <div class="mb-3">
                        <?= $form->field($model, 'username', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128100;'),
                            'inputOptions' => [
                                'class' => 'form-control',
                                'placeholder' => 'Usuario',
                                'autofocus' => true,
                            ],
                        ])->textInput(['maxlength' => 50])->label('Usuario', $labelOptions) ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'password', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128274;'),
                            'inputOptions' => [
                                'class' => 'form-control',
                                'placeholder' => 'Contraseña',
                            ],
                        ])->passwordInput(['maxlength' => 50])->label('Contraseña', $labelOptions) ?>
                    </div>

                    <div class="d-grid">
                        <?= Html::submitButton(
                            'Iniciar sesión',
                            [
                                'class' => 'btn login-btn btn-lg rounded-3 text-white',
                                'name' => 'login-button',
                            ],
                        ) ?>
                    </div>
                    <?= Html::a('¿Olvidaste tu contraseña?', ['site/request-password-reset'], ['class' => 'text-decoration-none']) ?>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>
</div>
