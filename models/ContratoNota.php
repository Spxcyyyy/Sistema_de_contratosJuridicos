<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class ContratoNota extends SoftDeleteRecord
{
    public function transactions()
    {
        return [self::SCENARIO_DEFAULT => self::OP_ALL];
    }
    public static function visibilityParents(): array
    {
        return ['contrato_id' => Contrato::class];
    }

    public static function tableName()
    {
        return 'contrato_nota';
    }

    public function rules()
    {
        return [
            [['contrato_id', 'user_id', 'contenido'], 'required'],
            [['contrato_id', 'user_id', 'created_at'], 'integer'],
            [['contenido'], 'string', 'max' => 50],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \app\models\behaviors\ActividadBehavior::class,
                'entidad' => 'nota',
                'campos' => ['contenido'],
            ],
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function getContrato()
    {
        return $this->hasOne(Contrato::class, ['id' => 'contrato_id']);
    }

    public function getAutor()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getNotas()
    {
        return $this->hasMany(ContratoNota::class, ['contrato_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }
}
