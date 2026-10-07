<?php

namespace app\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Firma;

/**
 * FirmaSearchs represents the model behind the search form of `app\models\Firma`.
 */
class FirmaSearchs extends Firma
{
    public $nomenclaturaContrato;
    public $codigoContrato;
    public $fechaRegistro;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'nomenclaturaContrato' => 'Nomenclatura',
            'codigoContrato' => 'Código del contrato',
            'fechaRegistro' => 'Fecha de registro',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'nomenclaturaContrato', 'codigoContrato'], 'string', 'max' => 50],
            [['id', 'contrato_id', 'created_at'], 'integer'],
            [['fechaRegistro'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Firma::find()->joinWith('contrato');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'nomenclaturaContrato' => [
                        'asc' => ['contratos.nomenclatura' => SORT_ASC],
                        'desc' => ['contratos.nomenclatura' => SORT_DESC],
                    ],
                    'codigoContrato' => [
                        'asc' => ['contratos.codigo' => SORT_ASC],
                        'desc' => ['contratos.codigo' => SORT_DESC],
                    ],
                    'nombre' => [
                        'asc' => ['firmas.nombre' => SORT_ASC],
                        'desc' => ['firmas.nombre' => SORT_DESC],
                    ],
                    'fechaRegistro' => [
                        'asc' => ['firmas.created_at' => SORT_ASC],
                        'desc' => ['firmas.created_at' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'firmas.id' => $this->id,
            'firmas.contrato_id' => $this->contrato_id,
            'firmas.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'firmas.nombre', $this->nombre])
            ->andFilterWhere(['like', 'contratos.nomenclatura', $this->nomenclaturaContrato])
            ->andFilterWhere(['like', 'contratos.codigo', $this->codigoContrato]);

        if ($this->fechaRegistro) {
            $inicio = new \DateTimeImmutable($this->fechaRegistro, new \DateTimeZone(Yii::$app->timeZone));
            $query->andWhere(['>=', 'firmas.created_at', $inicio->getTimestamp()])
                ->andWhere(['<', 'firmas.created_at', $inicio->modify('+1 day')->getTimestamp()]);
        }

        return $dataProvider;
    }
}
