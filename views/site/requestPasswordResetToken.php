<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Recuperar contraseña';
?>
<div class="site-request-password-reset">
    <div class="form-card" style="max-width: 400px; margin: 40px auto;">
        <h2 class="form-card-title">Recuperar contraseña</h2>
        <p>Ingresa tu nombre de usuario. Se avisará a un administrador para que atienda tu solicitud.</p>

        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']) ?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => 50]) ?>
        <div class="form-actions mt-3">
            <?= Html::submitButton('Enviar solicitud', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
