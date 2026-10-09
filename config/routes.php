<?php

return [
    '' => 'site/login',
    'iniciar-sesion' => 'site/login',
    'panel' => 'site/dashboard',
    'cerrar-sesion' => 'site/logout',
    'recuperar-contrasena' => 'site/request-password-reset',
    'acerca-de' => 'site/about',
    'error' => 'site/error',
    'contratos' => 'contrato/index',
    'contratos/nuevo' => 'contrato/create',
    'contratos/reportes' => 'contrato/reporte',
    'contratos/actividad' => 'contrato/actividad',
    'usuarios' => 'user/index',
    'usuarios/nuevo' => 'user/create',
    'firmas' => 'firma/index',
    'firmas/detalle' => 'firma/view',
    'firmas/seleccionar' => 'firma/seleccionar',
    ['class' => \app\components\PublicRecordUrlRule::class],
];
