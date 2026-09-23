<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\models\ContratoNota;

/**
 * This is the model class for table "contratos".
 *
 * @property int $id
 * @property string $codigo
 * @property string $encargado
 * @property string $nomenclatura
 * @property string $fecha_documento
 * @property string $estado
 * @property float $costo
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Firmas[] $firmas
 */
class Contrato extends SoftDeleteRecord
{
    public const ESTADOS_CONCLUIDOS = ['Firmado', 'Todas las firmas recabadas'];

    public static function find()
    {
        return new ContratoQuery(static::class);
    }

    protected function softDeleteRelated(): void
    {
        // Preserve each signature/note and its business state; the parent deletion is audited.
        foreach ([Firma::class, ContratoNota::class] as $class) {
            $class::updateAll(
                ['status_registro' => self::REGISTRO_ELIMINADO],
                ['contrato_id' => $this->id, 'status_registro' => self::REGISTRO_ACTIVO]
            );
        }
    }
    public function transactions()
    {
        return [self::SCENARIO_DEFAULT => self::OP_ALL];
    }

    public function getAvisoVencimiento()
    {
        if (!$this->fecha_vencimiento || in_array($this->estado, array_merge(self::ESTADOS_CONCLUIDOS, ['Cancelado']), true)) {
            return null;
        }
        $dias = (int) (new \DateTimeImmutable('today'))->diff(new \DateTimeImmutable($this->fecha_vencimiento))->format('%r%a');
        if ($dias < 0) {
            return ['clase' => 'danger', 'texto' => 'Vencido hace ' . abs($dias) . ' día(s)'];
        }
        if ($dias <= 7) {
            return ['clase' => 'warning', 'texto' => $dias === 0 ? 'Vence hoy' : 'Vence en ' . $dias . ' día(s)'];
        }
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contratos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_vencimiento'], 'default', 'value' => null],
            [['fecha_vencimiento'], 'date', 'format' => 'php:Y-m-d'],
            [['estado'], 'default', 'value' => 'En proceso de firmas'],
            [['costo'], 'default', 'value' => 0.00],
            [['codigo', 'encargado', 'nomenclatura', 'fecha_documento'], 'required'],
            [['fecha_documento'], 'safe'],
            [['costo'], 'number'],
            [['created_at', 'updated_at'], 'integer'],
            [['codigo', 'estado'], 'string', 'max' => 50],
            [['encargado', 'nomenclatura'], 'string', 'max' => 50],
            [['encargado'], 'match', 'pattern' => '/\A(?=.*\p{L})[\p{L}\p{M} ]+\z/u',
                'message' => 'El nombre de la encargada solo puede contener letras y espacios.',
                'enableClientValidation' => false,
            ],
            [['codigo'], 'unique', 'filter' => static fn($query) => $query->withDeleted()],
            [['ubicacion_archivo'], 'string', 'max' => 50],
            [['ubicacion_archivo'], 'default', 'value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'encargado' => 'Encargado',
            'nomenclatura' => 'Nomenclatura',
            'fecha_documento' => 'Fecha Documento',
            'fecha_vencimiento' => 'Fecha límite',
            'estado' => 'Estado',
            'costo' => 'Costo',
            'created_at' => 'Hora de creación',
            'updated_at' => 'Última actualización',
            'ubicacion_archivo' => 'Ubicación en archivo',
        ];
    }


    /** Genera el código CONV-YYYY-NNN automáticamente */
    public static function generarCodigo()
    {
        $anio = date('Y');
        $ultimo = static::find()->withDeleted()
            ->where(['like', 'codigo', "CONV-{$anio}-"])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $num = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->codigo);
            $num = (int) end($partes) + 1;
        }
        return sprintf('CONV-%s-%03d', $anio, $num);
    }


    /**
     * Gets query for [[Firmas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFirmas()
    {
        return $this->hasMany(Firma::class, ['contrato_id' => 'id']);
    }


    public function getNotas()
    {
        return $this->hasMany(ContratoNota::class, ['contrato_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Si todas las firmas asociadas están en estado 'firmado',
     * actualiza el estado del contrato.
     */
    public function actualizarEstadoSiCompleto()
    {
        $totalFirmas = (int) $this->getFirmas()->count();

        if ($this->estado === 'Cancelado') {
            return false;
        }

        $pendientes = (int) $this->getFirmas()
            ->where(['<>', 'estado', 'firmado'])
            ->count();

        if ($totalFirmas > 0 && $pendientes === 0 && !in_array($this->estado, self::ESTADOS_CONCLUIDOS, true)) {
            $this->estado = 'Todas las firmas recabadas';
            if (!$this->save()) {
                throw new \RuntimeException('No se pudo validar el estado actualizado del contrato.');
            }
            return true;
        }

        if (($totalFirmas === 0 || $pendientes > 0) && $this->estado === 'Todas las firmas recabadas') {
            $this->estado = 'En proceso de firmas';
            if (!$this->save()) {
                throw new \RuntimeException('No se pudo validar el estado actualizado del contrato.');
            }
            return true;
        }

        return false;
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => \app\models\behaviors\ActividadBehavior::class,
                'entidad' => 'contrato',
                'campos' => ['codigo', 'encargado', 'nomenclatura', 'fecha_documento', 'fecha_vencimiento', 'estado', 'costo', 'ubicacion_archivo'],
            ],
        ];
    }


}
