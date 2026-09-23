<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "firmas".
 *
 * @property int $id
 * @property int $contrato_id
 * @property string $nombre
 * @property int $created_at
 *
 * @property Contratos $contrato
 */
class Firma extends SoftDeleteRecord
{
    public function transactions()
    {
        return [self::SCENARIO_DEFAULT => self::OP_ALL];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Contrato::findOne($this->contrato_id)?->actualizarEstadoSiCompleto();
        if (isset($changedAttributes['contrato_id']) && $changedAttributes['contrato_id'] != $this->contrato_id) {
            Contrato::findOne($changedAttributes['contrato_id'])?->actualizarEstadoSiCompleto();
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Contrato::findOne($this->contrato_id)?->actualizarEstadoSiCompleto();
    }


    /**
     * {@inheritdoc}
     */
    public static function visibilityParents(): array
    {
        return ['contrato_id' => Contrato::class];
    }

    public static function tableName()
    {
        return 'firmas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contrato_id', 'nombre'], 'required'],
            [['contrato_id', 'fecha_firma'], 'integer'],
            [['nombre'], 'string', 'max' => 50],
            [['nombre'], 'match', 'pattern' => '/\A(?=.*\p{L})[\p{L}\p{M} ]+\z/u',
                'message' => 'El nombre del firmante solo puede contener letras y espacios.',
                'enableClientValidation' => false,
            ],
            [['estado'], 'string', 'max' => 20],
            [['estado'], 'in', 'range' => ['pendiente', 'firmado']],
            [['estado'], 'default', 'value' => 'pendiente'],
            [['contrato_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::class, 'targetAttribute' => ['contrato_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contrato_id' => 'Contrato ID',
            'nombre' => 'Nombre',
            'estado' => 'Estado',
            'fecha_firma' => 'Fecha de firma',
            'created_at' => 'Fecha de registro',
        ];
    }

    /**
     * Gets query for [[Contrato]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContrato()
    {
        return $this->hasOne(Contrato::class, ['id' => 'contrato_id']);
    }

    public function marcarComoFirmado()
    {
        if ($this->estado === 'firmado') {
            return true;
        }
        $previousState = $this->estado;
        $previousDate = $this->fecha_firma;
        $this->estado = 'firmado';
        $this->fecha_firma = time();
        $saved = $this->save();
        if (!$saved) {
            $this->estado = $previousState;
            $this->fecha_firma = $previousDate;
        }

        if ($saved && $this->contrato) {
            $this->contrato->refresh();
        }

        return $saved;
    }

    public function behaviors()
    {
        return [
            [
                'class' => \app\models\behaviors\ActividadBehavior::class,
                'entidad' => 'firma',
                'campos' => ['contrato_id', 'nombre', 'estado', 'fecha_firma'],
            ],
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }
}
