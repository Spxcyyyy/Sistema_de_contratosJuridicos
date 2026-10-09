<?php

use yii\helpers\Html;

/** @var yii\base\Model $searchModel */
/** @var array<string, string> $filters */

$params = Yii::$app->request->queryParams;
$formName = $searchModel->formName();
$applied = $params[$formName] ?? [];
$links = [];

if (is_array($applied)) {
    foreach ($filters as $attribute => $label) {
        $value = $applied[$attribute] ?? null;
        if (!is_scalar($value) || trim((string) $value) === '') {
            continue;
        }

        $remaining = $params;
        unset($remaining[$formName][$attribute], $remaining['pagina'], $remaining['page']);
        if ($remaining[$formName] === []) {
            unset($remaining[$formName]);
        }

        $links[] = Html::a(
            Html::encode($label) . ' <span aria-hidden="true">×</span>',
            array_merge(['index'], $remaining),
            ['class' => 'btn btn-sm btn-outline-secondary', 'aria-label' => 'Quitar filtro de ' . $label]
        );
    }
}
?>
<?php if ($links !== []): ?>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3" aria-label="Filtros activos">
        <span class="text-muted small">Filtros activos:</span>
        <?= implode(' ', $links) ?>
    </div>
<?php endif; ?>
