<?php

use yii\helpers\Html;

/** @var app\models\ContratoActividad $model */

$descripciones = [
    'contrato' => ['creado' => 'Contrato creado', 'actualizado' => 'Contrato actualizado', 'eliminado' => 'Contrato eliminado'],
    'firma' => ['creado' => 'Firmante agregado', 'actualizado' => 'Firmante actualizado', 'eliminado' => 'Firmante eliminado', 'firmado' => 'Firma recabada', 'desmarcado' => 'Firma desmarcada'],
    'nota' => ['creado' => 'Nota agregada', 'actualizado' => 'Nota actualizada', 'eliminado' => 'Nota eliminada'],
];
$descripcion = $descripciones[$model->entidad][$model->accion] ?? $model->descripcion;
$sinValor = static fn($valor): bool => $valor === null || trim((string) $valor) === '';
$mostrarValor = static function (string $campo, $valor): string {
    $texto = trim((string) $valor);
    if (in_array($campo, ['fecha_documento', 'fecha_vencimiento'], true)) {
        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $texto);
        if ($fecha !== false && $fecha->format('Y-m-d') === $texto) {
            return $fecha->format('d/m/Y');
        }
    }
    if ($campo === 'fecha_firma' && ctype_digit($texto)) {
        return Yii::$app->formatter->asDatetime((int) $texto);
    }
    return $texto;
};
$detalle = '';
if (in_array($model->accion, ['creado', 'eliminado'], true)) {
    $campoPrincipal = match ($model->entidad) {
        'contrato' => 'codigo',
        'firma' => 'nombre',
        'nota' => 'contenido',
        default => null,
    };
    $cambioPrincipal = $campoPrincipal ? ($model->detalleCambios[$campoPrincipal] ?? null) : null;
    $valor = $cambioPrincipal[$model->accion === 'creado' ? 'despues' : 'antes'] ?? null;
    if (!$sinValor($valor)) {
        $detalle = $mostrarValor($campoPrincipal, $valor);
    }
} elseif (!in_array($model->accion, ['firmado', 'desmarcado'], true)) {
    $partes = [];
    foreach ($model->detalleCambios as $campo => $cambio) {
        if ($campo === 'contrato_id' || ($sinValor($cambio['antes']) && $sinValor($cambio['despues']))) {
            continue;
        }
        $etiqueta = $cambio['etiqueta'];
        if ($sinValor($cambio['antes'])) {
            $partes[] = $etiqueta . ': ' . $mostrarValor($campo, $cambio['despues']);
        } elseif ($sinValor($cambio['despues'])) {
            $partes[] = $etiqueta . ': ' . $mostrarValor($campo, $cambio['antes']) . ' → retirado';
        } else {
            $partes[] = $etiqueta . ': ' . $mostrarValor($campo, $cambio['antes']) . ' → ' . $mostrarValor($campo, $cambio['despues']);
        }
    }
    $detalle = implode(' · ', $partes);
}
$resumen = $descripcion . ($detalle !== '' ? ' · ' . $detalle : '');
?>
<article class="actividad-item actividad-item-compacto">
    <div class="actividad-body">
        <div class="actividad-heading">
            <strong><?= Html::encode($model->actor_nombre) ?></strong>
            <time datetime="<?= date('c', $model->created_at) ?>"><?= Html::encode(Yii::$app->formatter->asDatetime($model->created_at)) ?></time>
        </div>
        <p class="actividad-meta"><?= Html::encode($resumen) ?></p>
    </div>
</article>
