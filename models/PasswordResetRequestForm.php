<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\PasswordResetRequest;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            [['email'], 'string', 'max' => 50],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'No existe un usuario con este correo.',
            ],
        ];
    }

    /**
     * Ya no envía correo: crea la solicitud para que el admin la vea en su CRUD.
     */
    public function sendEmail()
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

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
        $request->email = $this->email;
        $request->status = PasswordResetRequest::STATUS_PENDING;
        $request->created_at = time();

        return $request->save();
    }
}