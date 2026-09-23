<?php

declare(strict_types=1);

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class UserController extends Controller
{
    /**
     * Crea un usuario con rol admin.
     * Uso: php yii user/create-admin <username> <email> <password>
     */
    public function actionCreateAdmin(string $username, string $email, string $password): int
    {
        $user = new User(['scenario' => User::SCENARIO_CREATE]);
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;
        $user->role = User::ROLE_ADMIN;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($password);
        $user->generateAuthKey();

        if ($user->save()) {
            $this->stdout("Usuario admin '{$username}' creado correctamente (id={$user->id}).\n");
            return ExitCode::OK;
        }

        $this->stderr("Error al crear el usuario:\n");
        foreach ($user->getErrorSummary(true) as $error) {
            $this->stderr("- {$error}\n");
        }
        return ExitCode::DATAERR;
    }
}
