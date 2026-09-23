<?php

namespace app\models;

use yii\db\ActiveRecord;

/** Deletion preserves rows, business states, relations and audit history. */
abstract class SoftDeleteRecord extends ActiveRecord
{
    public const REGISTRO_ACTIVO = 'activo';
    public const REGISTRO_ELIMINADO = 'eliminado';

    public function beforeSave($insert)
    {
        if ($insert && $this->hasAttribute('referencia_publica')) {
            // Generated internally; never accept a public reference from a submitted form.
            $this->referencia_publica = bin2hex(random_bytes(12));
        }
        return parent::beforeSave($insert);
    }
    public static function find()
    {
        return new SoftDeleteQuery(static::class);
    }

    public static function visibilityParents(): array
    {
        return [];
    }

    protected function softDeleteRelated(): void
    {
    }

    public function delete()
    {
        if ($this->isNewRecord) {
            return false;
        }
        $transaction = static::getDb()->beginTransaction();
        $attributes = $this->getAttributes();
        $oldAttributes = $this->getOldAttributes();
        try {
            $condition = array_merge($this->getOldPrimaryKey(true), ['status_registro' => self::REGISTRO_ACTIVO]);
            if (!static::find()->withDeleted()->where($condition)->exists()) {
                $transaction->rollBack();
                return 0;
            }
            if (!$this->beforeDelete()) {
                $transaction->rollBack();
                return false;
            }
            $this->softDeleteRelated();
            $values = ['status_registro' => self::REGISTRO_ELIMINADO];
            if ($this->hasAttribute('updated_at')) {
                $values['updated_at'] = time();
            }
            $count = static::updateAll($values, $condition);
            if ($count === 0) {
                $transaction->rollBack();
                return 0;
            }
            foreach ($values as $name => $value) {
                $this->setAttribute($name, $value);
                $this->setOldAttribute($name, $value);
            }
            $this->afterDelete();
            $transaction->commit();
            return $count;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            $this->setAttributes($attributes, false);
            $this->setOldAttributes($oldAttributes);
            throw $error;
        }
    }

    /** Bulk removals also preserve rows and execute delete events/cascades atomically. */
    public static function deleteAll($condition = null, $params = [])
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $count = 0;
            foreach (static::find()->where($condition, $params)->all() as $model) {
                $deleted = $model->delete();
                if ($deleted === false) {
                    throw new \RuntimeException('No se pudo eliminar el registro.');
                }
                $count += $deleted;
            }
            $transaction->commit();
            return $count;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            throw $error;
        }
    }
}
