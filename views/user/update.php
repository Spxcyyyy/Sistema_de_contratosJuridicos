<?php

use yii\helpers\Html;
use app\components\ListReturnUrl;

$this->title = 'Editar: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ListReturnUrl::url('user/index')];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a('&larr; Regresar', ListReturnUrl::url('user/index'), ['class' => 'btn-back']) ?>
<h1 class="contrato-titulo mb-4"><?= Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>
