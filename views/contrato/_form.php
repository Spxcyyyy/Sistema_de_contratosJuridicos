<?php
$this->registerCssFile('@web/css/contrato.css');
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$firmas = $firmas ?? ($model->firmas ?: [new \app\models\Firma()]);
?>

<div class="contrato-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="form-card mb-4">
        <h2 class="form-card-title">Datos del contrato</h2>

        <div class="row g-3">
            <div class="col-md-4">
                <?= $form->field($model, 'codigo')->textInput(['readonly' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'encargado')->textInput([
                    'maxlength' => 50,
                    'data-letters-only' => 'true',
                ])->hint('Solo letras y espacios, máximo 50 caracteres.') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'nomenclatura')->textInput(['maxlength' => 50]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fecha_documento')->input('date') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fecha_vencimiento')->input('date')->hint('Opcional. Verás avisos desde 7 días antes y mientras esté vencido.') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'estado')->dropDownList([
                    'En proceso de firmas' => 'En proceso de firmas',
                    'Firmado' => 'Firmado',
                    'Todas las firmas recabadas' => 'Todas las firmas recabadas',
                    'Cancelado' => 'Cancelado',
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'costo')->input('number', ['step' => '0.01']) ?>
            </div>
        </div>
    </div>

    <div class="form-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="form-card-title mb-0">Firmantes</h2>
            <button type="button" id="btn-add-firma" class="btn btn-outline-guinda btn-sm">
                + Agregar firmante
            </button>
        </div>

        <div id="firmas-container">
            <?php foreach ($firmas as $i => $firma): ?>
            <div class="firma-row">
                <?= Html::hiddenInput("Firma[{$i}][id]", $firma->id) ?>
                <span class="firma-numero"><?= $i + 1 ?></span>
                <div class="flex-grow-1">
                    <?= Html::textInput("Firma[{$i}][nombre]", $firma->nombre, [
                        'id' => "firma-{$i}-nombre",
                        'maxlength' => 50,
                        'data-letters-only' => 'true',
                        'class' => 'form-control' . ($firma->hasErrors('nombre') ? ' is-invalid' : ''),
                        'placeholder' => 'Nombre del firmante',
                        'title' => 'Solo letras y espacios, máximo 50 caracteres',
                        'aria-invalid' => $firma->hasErrors('nombre') ? 'true' : 'false',
                        'aria-describedby' => $firma->hasErrors('nombre') ? "firma-{$i}-error" : null,
                    ]) ?>
                    <?= Html::error($firma, 'nombre', ['id' => "firma-{$i}-error", 'class' => 'invalid-feedback']) ?>
                </div>
                <button type="button" class="btn-remove-firma" title="Quitar firmante">&times;</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-actions">
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-link text-secondary']) ?>
        <?= Html::submitButton('Guardar contrato', ['class' => 'btn btn-primary px-4']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = <?= count($firmas) ?>;
    const container = document.getElementById('firmas-container');

    function renumerar() {
        container.querySelectorAll('.firma-row').forEach((row, i) => {
            row.querySelector('.firma-numero').textContent = i + 1;
        });
    }

    document.getElementById('btn-add-firma').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'firma-row';
        row.innerHTML = `
            <span class="firma-numero"></span>
            <input type="text" maxlength="50" data-letters-only="true" name="Firma[${index}][nombre]" class="form-control" placeholder="Nombre del firmante" title="Solo letras y espacios, máximo 50 caracteres">
            <button type="button" class="btn-remove-firma" title="Quitar firmante">&times;</button>
        `;
        container.appendChild(row);
        index++;
        renumerar();
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-firma')) {
            e.target.closest('.firma-row').remove();
            renumerar();
        }
    });
});
</script>
