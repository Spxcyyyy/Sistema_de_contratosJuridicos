<?php

namespace app\models;

use Yii;

/**
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int|null $resolved_at
 * @property int|null $resolved_by
 */
class PasswordResetRequest extends SoftDeleteRecord
{
    const STATUS_PENDING = 0;
    const STATUS_RESOLVED = 1;

    public static function visibilityParents(): array
    {
        return ['user_id' => User::class];
    }

    public static function tableName()
    {
        return '{{%password_reset_request}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'email'], 'required'],
            [['user_id', 'status', 'created_at', 'resolved_at', 'resolved_by'], 'integer'],
            ['email', 'string', 'max' => 50],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function pendingCount()
    {
        return static::find()->where(['status' => self::STATUS_PENDING])->count();
    }

    public function markResolved($adminId)
    {
        $this->status = self::STATUS_RESOLVED;
        $this->resolved_at = time();
        $this->resolved_by = $adminId;
        return $this->save();
    }
}