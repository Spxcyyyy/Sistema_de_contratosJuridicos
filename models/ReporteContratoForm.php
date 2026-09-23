<?php

namespace app\models;

use yii\base\Model;

class ReporteContratoForm extends Model
{
    public const COLUMNAS = [
        'codigo' => 'Código', 'encargado' => 'Encargado', 'nomenclatura' => 'Nomenclatura',
        'fecha_documento' => 'Fecha del documento', 'fecha_vencimiento' => 'Fecha límite',
        'estado' => 'Estado', 'costo' => 'Costo', 'created_at' => 'Hora de creación',
    ];
    public $alcance = 'todos';
    public $ids_json = '[]';
    public $desde;
    public $hasta;
    public $columnas = ['codigo', 'encargado', 'nomenclatura', 'fecha_documento', 'estado', 'costo', 'created_at'];
    public $formato = 'pdf';
    private array $ids = [];

    public function rules()
    {
        return [
            [['alcance'], 'required'],
            [['alcance'], 'in', 'range' => ['todos', 'seleccion']],
            [['formato'], 'required'],
            [['formato'], 'in', 'range' => ['pdf', 'xlsx', 'csv']],
            [['columnas'], 'required', 'message' => 'Selecciona al menos una columna.'],
            [['columnas'], 'each', 'rule' => ['in', 'range' => array_keys(self::COLUMNAS)]],
            [['ids_json'], 'validateSelection', 'skipOnEmpty' => false],
            [['desde', 'hasta'], 'date', 'format' => 'php:Y-m-d', 'when' => fn() => $this->alcance === 'todos'],
            [['hasta'], 'validateRange', 'when' => fn() => $this->alcance === 'todos'],
        ];
    }

    public function validateRange($attribute)
    {
        if (!$this->hasErrors('desde') && !$this->hasErrors('hasta') && $this->desde && $this->hasta && $this->desde > $this->hasta) {
            $this->addError($attribute, 'La fecha final debe ser igual o posterior a la inicial.');
        }
    }

    public function validateSelection($attribute)
    {
        $this->ids = [];
        if ($this->alcance !== 'seleccion') { return; }
        $raw = is_string($this->ids_json) && strlen($this->ids_json) <= 12000 ? json_decode($this->ids_json, true) : null;
        if (!is_array($raw) || !array_is_list($raw) || !$raw || count($raw) > 500) {
            $this->addError($attribute, 'Selecciona entre 1 y 500 contratos desde el listado.');
            return;
        }
        foreach ($raw as $id) {
            if ((!is_int($id) && !is_string($id)) || !preg_match('/^[1-9][0-9]{0,9}$/D', (string) $id) || (int) $id > 2147483647) {
                $this->addError($attribute, 'La selección contiene identificadores inválidos.');
                return;
            }
            $this->ids[] = (int) $id;
        }
        $this->ids = array_values(array_unique($this->ids));
        if ((int) Contrato::find()->where(['id' => $this->ids])->count() !== count($this->ids)) {
            $this->addError($attribute, 'Uno o más contratos ya no existen. Regresa al listado y actualiza la selección.');
        }
    }

    public function getIds(): array { return $this->ids; }

    public function crearConsulta()
    {
        $query = Contrato::find();
        if ($this->alcance === 'seleccion') {
            $query->andWhere(['id' => $this->ids]);
        } else {
            $query->andFilterWhere(['>=', 'fecha_documento', $this->desde]);
            $query->andFilterWhere(['<=', 'fecha_documento', $this->hasta]);
        }
        return $query->orderBy(['fecha_documento' => SORT_DESC, 'id' => SORT_DESC]);
    }

    public function attributeLabels()
    {
        return ['ids_json' => 'Contratos seleccionados', 'desde' => 'Desde', 'hasta' => 'Hasta', 'columnas' => 'Columnas', 'formato' => 'Formato'];
    }
}
