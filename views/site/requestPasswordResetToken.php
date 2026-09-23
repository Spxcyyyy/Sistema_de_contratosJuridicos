<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Recuperar contraseña';
?>
<div class="site-request-password-reset">
    <div class="form-card" style="max-width: 400px; margin: 40px auto;">
        <h2 class="form-card-title">Recuperar contraseña</h2>
        <p>Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']) ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => 50]) ?>
        <div class="form-actions mt-3">
            <?= Html::submitButton('Enviar', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>