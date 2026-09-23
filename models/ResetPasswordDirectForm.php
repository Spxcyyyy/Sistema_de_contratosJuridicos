<?php

namespace app\models;

use yii\base\Model;

class ResetPasswordDirectForm extends Model
{
    public $newPassword;

    public function rules()
    {
        return [
            ['newPassword', 'required'],
            ['newPassword', 'string', 'min' => 8, 'max' => 50],
        ];
    }
}