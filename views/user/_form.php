<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use app\models\User;

$isCreate = $model->isNewRecord;
?>
<div class="user-form">
    <?php $form = ActiveForm::begin() ?>

    <div class="form-card mb-4">
        <h2 class="form-card-title">Datos del usuario</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <?= $form->field($model, 'username')->textInput(['maxlength' => 50]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => 50]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'role')->dropDownList([
                    User::ROLE_USUARIO => 'Usuario',
                    User::ROLE_ADMIN => 'Administrador',
                    User::ROLE_RECABADOR => 'Recabador',
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropDownList([
                    User::STATUS_ACTIVE => 'Activo',
                    User::STATUS_INACTIVE => 'Inactivo',
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => 50])
                    ->hint($isCreate ? 'Entre 8 y 50 caracteres.' : 'Déjalo vacío para no cambiar la contraseña actual.') ?>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-link text-secondary']) ?>
        <?= Html::submitButton($isCreate ? 'Crear usuario' : 'Guardar cambios', ['class' => 'btn btn-primary px-4']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>