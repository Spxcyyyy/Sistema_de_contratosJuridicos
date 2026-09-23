<?php

namespace app\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contrato;

class ContratoSearchs extends Contrato
{
    public static function estadosDisponibles(): array
    {
        $estados = Contrato::find()
            ->select('estado')
            ->distinct()
            ->andWhere(['<>', 'estado', ''])
            ->orderBy(['estado' => SORT_ASC])
            ->column();

        return array_combine($estados, $estados);
    }

    public $grupo;
    public $fecha_documento_desde;
    public $fecha_documento_hasta;
    public $created_at_desde;
    public $created_at_hasta;

    public function rules()
    {
        return [
            [['codigo', 'encargado', 'nomenclatura', 'estado', 'fecha_documento', 'fecha_documento_desde', 'fecha_documento_hasta', 'created_at_desde', 'created_at_hasta'], 'string', 'max' => 50],
            [['grupo'], 'in', 'range' => ['proceso', 'concluidos', 'vencidos', 'proximos', 'firmas']],
            [['id'], 'integer'],
            [['fecha_documento', 'fecha_vencimiento'], 'date', 'format' => 'php:Y-m-d'],
            [['estado'], 'in', 'range' => array_keys(self::estadosDisponibles())],
            [['codigo', 'encargado', 'nomenclatura', 'estado', 'fecha_documento'], 'safe'],
            [['fecha_documento_desde', 'fecha_documento_hasta', 'created_at_desde', 'created_at_hasta'], 'safe'],
            [['costo'], 'number'],
        ];
    }

    public function search($params)
    {
        $query = Contrato::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC, 'id' => SORT_DESC]],
        ]);

        $this->load($params);

        // Dashboard links may still apply a group, but groups are not state options.
        // Choosing a state (or Todos) replaces that dashboard filter.
        $submitted = $params[$this->formName()] ?? [];
        if (!$this->validate()) {
            $query->andWhere('0=1');
            return $dataProvider;
        }

        if (!array_key_exists('estado', $submitted)) {
            $query->grupo($this->grupo);
        }
        $query->andFilterWhere(['estado' => $this->estado]);

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'encargado', $this->encargado])
            ->andFilterWhere(['like', 'nomenclatura', $this->nomenclatura])
            ->andFilterWhere(['fecha_documento' => $this->fecha_documento])
            ->andFilterWhere(['fecha_vencimiento' => $this->fecha_vencimiento]);

        // Rango de fecha_documento
        if (!empty($this->fecha_documento_desde)) {
            $query->andFilterWhere(['>=', 'fecha_documento', $this->fecha_documento_desde]);
        }
        if (!empty($this->fecha_documento_hasta)) {
            $query->andFilterWhere(['<=', 'fecha_documento', $this->fecha_documento_hasta]);
        }

        // Rango de created_at
        if (!empty($this->created_at_desde)) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->created_at_desde . ' 00:00:00')]);
        }
        if (!empty($this->created_at_hasta)) {
            $query->andFilterWhere(['<=', 'created_at', strtotime($this->created_at_hasta . ' 23:59:59')]);
        }

        return $dataProvider;
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

}
