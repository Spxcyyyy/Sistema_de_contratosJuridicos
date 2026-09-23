<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

class User extends SoftDeleteRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_ADMIN = 'admin';
    const ROLE_USUARIO = 'usuario';
    const ROLE_RECABADOR = 'recabador';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $password;

    public static function tableName()
    {
        return 'user';
    }

    protected function softDeleteRelated(): void
    {
        PasswordResetRequest::updateAll(
            ['status_registro' => self::REGISTRO_ELIMINADO],
            ['user_id' => $this->id, 'status_registro' => self::REGISTRO_ACTIVO]
        );
    }
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['email'], 'string', 'max' => 50],
            [['username', 'email'], 'required'],
            [['username'], 'string', 'max' => 50],
            [['username'], 'unique', 'filter' => static fn($query) => $query->withDeleted()],
            [['email'], 'email'],
            [['email'], 'unique', 'filter' => static fn($query) => $query->withDeleted()],
            [['role'], 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_USUARIO, self::ROLE_RECABADOR]],
            [['role'], 'default', 'value' => self::ROLE_USUARIO],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['password'], 'required', 'on' => self::SCENARIO_CREATE],
            [['password'], 'string', 'min' => 8, 'max' => 50],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['username', 'email', 'role', 'status', 'password'];
        $scenarios[self::SCENARIO_UPDATE] = ['username', 'email', 'role', 'status', 'password'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Usuario',
            'email' => 'Correo',
            'role' => 'Rol',
            'status' => 'Estado',
            'password' => 'Contraseña',
            'created_at' => 'Fecha de creación',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600;
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }


    public function isRecabador()
    {
        return $this->role === self::ROLE_RECABADOR;
    }
}