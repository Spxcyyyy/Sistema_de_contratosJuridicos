<?php

use yii\helpers\Html;

$this->title = 'Editar: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 class="contrato-titulo mb-4"><?= Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>