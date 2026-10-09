<?php

namespace app\components;

use Yii;
use app\models\User;
use yii\filters\AccessControl;

/** Lista explícita de permisos compartida por los filtros de los controladores y las acciones visibles en la interfaz. */
final class AccessPolicy
{
    private const READERS = [User::ROLE_ADMIN, User::ROLE_JURIDICOS, User::ROLE_RECABADOR];
    private const EDITORS = [User::ROLE_ADMIN, User::ROLE_JURIDICOS];
    private const COLLECTORS = [User::ROLE_ADMIN, User::ROLE_RECABADOR];

    public const PERMISSIONS = [
        'site/dashboard' => self::EDITORS,
        'contrato/index' => self::READERS,
        'contrato/view' => self::READERS,
        'contrato/actividad' => self::EDITORS,
        'contrato/reporte' => self::EDITORS,
        'contrato/create' => self::EDITORS,
        'contrato/update' => self::EDITORS,
        'contrato/delete' => self::EDITORS,
        'contrato/add-nota' => self::COLLECTORS,
        'contrato/marcar-firmado' => self::READERS,
        'contrato/desmarcar-firmado' => self::READERS,
        'firma/index' => self::EDITORS,
        'firma/seleccionar' => self::EDITORS,
        'firma/view' => self::EDITORS,
        'user/index' => [User::ROLE_ADMIN],
        'user/view' => [User::ROLE_ADMIN],
        'user/create' => [User::ROLE_ADMIN],
        'user/update' => [User::ROLE_ADMIN],
        'user/delete' => [User::ROLE_ADMIN],
        'user/reset-password' => [User::ROLE_ADMIN],
    ];

    public static function allows(string $route): bool
    {
        $identity = Yii::$app->user->identity;
        return $identity instanceof User
            && (int) $identity->status === User::STATUS_ACTIVE
            && $identity->status_registro !== User::REGISTRO_ELIMINADO
            && in_array($identity->role, self::PERMISSIONS[ltrim($route, '/')] ?? [], true);
    }

    public static function accessBehavior(): array
    {
        return [
            'class' => AccessControl::class,
            'rules' => [[
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => static fn($rule, $action) => self::allows($action->uniqueId),
            ]],
        ];
    }
}
