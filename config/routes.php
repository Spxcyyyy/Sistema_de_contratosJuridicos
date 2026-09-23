<?php

return [
    '' => 'site/index',
    'iniciar-sesion' => 'site/login',
    'cerrar-sesion' => 'site/logout',
    'recuperar-contrasena' => 'site/request-password-reset',
    'contacto' => 'site/contact',
    'acerca-de' => 'site/about',
    'captcha' => 'site/captcha',
    'error' => 'site/error',
    'contratos' => 'contrato/index',
    'contratos/nuevo' => 'contrato/create',
    'contratos/reportes' => 'contrato/reporte',
    'contratos/actividad' => 'contrato/actividad',
    'usuarios' => 'user/index',
    'usuarios/nuevo' => 'user/create',
    'firmas' => 'firma/index',
    'firmas/nueva' => 'firma/create',
    ['class' => \app\components\PublicRecordUrlRule::class],
];
