<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Restablecer contraseña';
?>
<h2>Restablecer contraseña de <?= Html::encode($user->username) ?></h2>

<?php $form = ActiveForm::begin() ?>

<div class="input-group mb-3">
    <?= $form->field($model, 'newPassword', [
        'template' => '{input}{error}',
    ])->textInput([
        'type' => 'password',
        'maxlength' => 50,
        'id' => 'newpassword-input',
        'class' => 'form-control',
    ]) ?>
    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
        <svg id="icon-eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
        <svg id="icon-eye-closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.9 18.9 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.9 18.9 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        </svg>
    </button>
</div>

<?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>

<script>
function togglePassword() {
    const input = document.getElementById('newpassword-input');
    const openIcon = document.getElementById('icon-eye-open');
    const closedIcon = document.getElementById('icon-eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        openIcon.style.display = 'none';
        closedIcon.style.display = 'inline';
    } else {
        input.type = 'password';
        openIcon.style.display = 'inline';
        closedIcon.style.display = 'none';
    }
}
</script>