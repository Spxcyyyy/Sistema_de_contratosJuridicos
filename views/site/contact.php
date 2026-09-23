<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\ContactForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

$this->title = 'Contacto';
$this->params['breadcrumbs'][] = $this->title;
$this->params['meta_description'] = 'Ponte en contacto con el equipo responsable del sistema.';
$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>
<?php if (Yii::$app->session->hasFlash('success')): ?>

<div class="site-contact-success d-flex align-items-center justify-content-center text-center">
    <div class="site-contact-success-content mx-auto">
        <h1 class="display-6 fw-semibold mb-3">Mensaje enviado</h1>

        <p class="text-body-secondary mb-4">
            Gracias por escribirnos. Te responderemos a la brevedad.
        </p>

        <?= Html::a(
            'Enviar otro mensaje',
            ['contact'],
            ['class' => 'btn btn-outline-primary btn-lg'],
        ) ?>
    </div>
</div>

<?php else: ?>

<div class="site-contact d-flex align-items-center justify-content-center py-5">
    <div class="card border-0 overflow-hidden login-split-card login-split-card-wide">
        <div class="row g-0">

            <!-- Brand panel -->
            <div class="col-md-4 d-none d-md-flex login-brand-panel text-white">
                <div class="d-flex flex-column justify-content-between p-4 p-lg-5 w-100">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="1.4"/>
                            <circle cx="12" cy="12" r="7.6" stroke="currentColor" stroke-width="1" stroke-dasharray="1.4 2"/>
                            <path d="M9 12.6l2 2 4.3-4.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="fw-semibold"><?= Html::encode(Yii::$app->params['appShortName']) ?></span>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-3 login-brand-title">
                            Estamos para<br>ayudarte
                        </h2>
                        <p class="opacity-75 mb-0 login-brand-text">
                            ¿Tienes dudas o comentarios sobre el sistema? Escríbenos.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form panel -->
            <div class="col-md-8">
                <div class="p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
                        <p class="text-body-secondary small">Completa el formulario y te responderemos a la brevedad</p>
                    </div>

                    <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <?= $form->field($model, 'name', [
                                'options' => ['class' => 'mb-0'],
                                'template' => sprintf($htmlIcon, '&#128100;'),
                                'inputOptions' => ['maxlength' => 50,
                                    'class' => 'form-control',
                                    'placeholder' => 'Nombre',
                                    'autofocus' => true,
                                ],
                            ])->label('Tu nombre', $labelOptions) ?>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <?= $form->field($model, 'email', [
                                'options' => ['class' => 'mb-0'],
                                'template' => sprintf($htmlIcon, '&#9993;'),
                                'inputOptions' => ['maxlength' => 50,
                                    'class' => 'form-control',
                                    'placeholder' => 'correo@ejemplo.com',
                                ],
                            ])->label('Tu correo', $labelOptions) ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'subject', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128172;'),
                            'inputOptions' => ['maxlength' => 50,
                                'class' => 'form-control',
                                'placeholder' => 'Asunto',
                            ],
                        ])->label('Asunto', $labelOptions) ?>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'body', [
                            'options' => ['class' => 'mb-0'],
                            'template' => '{label}{input}{error}{hint}',
                            'inputOptions' => ['maxlength' => 50,
                                'class' => 'form-control',
                                'placeholder' => 'Tu mensaje...',
                            ],
                        ])->textarea(['maxlength' => 50])->label('Mensaje', $labelOptions) ?>
                    </div>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <?= $form->field($model, 'verifyCode', [
                            'enableLabel' => false,
                            'options' => ['class' => ''],
                            'inputOptions' => ['maxlength' => 50, 'aria-label' => 'Código de verificación'],
                        ])->widget(Captcha::class, ['options' => ['maxlength' => 50, 'class' => 'form-control'],
                            'template' => '<div class="d-flex align-items-center gap-2">{image}{input}</div>',
                        ]) ?>

                        <?= Html::submitButton(
                            'Enviar',
                            [
                                'class' => 'btn login-btn text-white px-4 ms-auto',
                                'name' => 'contact-button',
                            ],
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>
</div>

<?php endif; ?>
