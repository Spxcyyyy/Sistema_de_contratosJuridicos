<?php

namespace app\models;

use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    public $username;

    public function rules()
    {
        return [
            [['username'], 'string', 'max' => 50],
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'No existe un usuario activo con ese nombre de usuario.',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return ['username' => 'Nombre de usuario'];
    }

    /**
     * Registra la solicitud para que el administrador la vea en Usuarios.
     */
    public function createRequest(): bool
    {
        $user = User::findByUsername($this->username);

        if (!$user) {
            return false;
        }

        $existing = PasswordResetRequest::find()
            ->where(['user_id' => $user->id, 'status' => PasswordResetRequest::STATUS_PENDING])
            ->exists();

        if ($existing) {
            return true;
        }

        $request = new PasswordResetRequest();
        $request->user_id = $user->id;
        $request->email = $user->email;
        $request->status = PasswordResetRequest::STATUS_PENDING;
        $request->created_at = time();

        return $request->save();
    }
}
