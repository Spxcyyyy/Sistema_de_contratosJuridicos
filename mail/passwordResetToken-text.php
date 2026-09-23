<?php
use yii\helpers\Url;
?>
Hola <?= $user->username ?>,

Sigue este enlace para restablecer tu contraseña:
<?= Url::to(['site/reset-password', 'token' => $user->password_reset_token], true) ?>

Si tú no solicitaste esto, puedes ignorar este correo.