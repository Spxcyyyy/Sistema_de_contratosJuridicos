<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Restablecer contraseña';
?>
<div class="site-reset-password">
    <div class="form-card" style="max-width: 400px; margin: 40px auto;">
        <h2 class="form-card-title">Nueva contraseña</h2>

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']) ?>
        <?= $form->field($model, 'password')->passwordInput(['maxlength' => 50]) ?>
        <div class="form-actions mt-3">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>