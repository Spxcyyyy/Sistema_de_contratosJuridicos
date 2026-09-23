<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<p>Hola <?= Html::encode($user->username) ?>,</p>
<p>Sigue este enlace para restablecer tu contraseña:</p>
<p><?= Html::a('Restablecer contraseña', Url::to(['site/reset-password', 'token' => $user->password_reset_token], true)) ?></p>
<p>Si tú no solicitaste esto, puedes ignorar este correo.</p>