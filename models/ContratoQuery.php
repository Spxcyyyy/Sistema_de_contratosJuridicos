<?php

namespace app\models;

use yii\db\Query;

class ContratoQuery extends SoftDeleteQuery
{
    public function enProceso()
    {
        return $this->andWhere(['not in', 'contratos.estado', array_merge(Contrato::ESTADOS_CONCLUIDOS, ['Cancelado'])]);
    }

    public function concluidos()
    {
        return $this->andWhere(['contratos.estado' => Contrato::ESTADOS_CONCLUIDOS]);
    }

    public function vencidos($hoy = null)
    {
        return $this->enProceso()->andWhere(['<', 'fecha_vencimiento', $hoy ?? date('Y-m-d')]);
    }

    public function proximos($hoy = null)
    {
        $hoy = $hoy ?? date('Y-m-d');
        $limite = (new \DateTimeImmutable($hoy))->modify('+7 days')->format('Y-m-d');
        return $this->enProceso()->andWhere(['between', 'fecha_vencimiento', $hoy, $limite]);
    }

    public function firmasPendientes()
    {
        return $this->enProceso()->andWhere(['exists', (new Query())->select('id')->from('firmas')
            ->where('firmas.contrato_id = contratos.id')->andWhere(['estado' => 'pendiente', 'status_registro' => SoftDeleteRecord::REGISTRO_ACTIVO])]);
    }

    public function grupo($grupo)
    {
        return match ($grupo) {
            'proceso' => $this->enProceso(),
            'concluidos' => $this->concluidos(),
            'vencidos' => $this->vencidos(),
            'proximos' => $this->proximos(),
            'firmas' => $this->firmasPendientes(),
            default => $this,
        };
    }
}
