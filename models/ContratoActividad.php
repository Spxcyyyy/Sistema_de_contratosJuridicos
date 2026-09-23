<?php

namespace app\models;


class ContratoActividad extends SoftDeleteRecord
{
    public static function visibilityParents(): array
    {
        return ['contrato_id' => Contrato::class];
    }

    public static function tableName()
    {
        return 'contrato_actividad';
    }

    public function rules()
    {
        return [
            [['codigo', 'actor_nombre', 'entidad', 'entidad_id', 'accion', 'cambios', 'created_at'], 'required'],
            [['contrato_id', 'actor_id', 'entidad_id', 'created_at'], 'integer'],
            [['codigo', 'actor_nombre'], 'string', 'max' => 255],
            [['entidad'], 'in', 'range' => ['contrato', 'firma', 'nota']],
            [['accion'], 'in', 'range' => ['creado', 'actualizado', 'eliminado', 'firmado']],
            [['cambios'], 'string'],
        ];
    }
    public function getDescripcion()
    {
        $acciones = ['creado' => 'Registro creado', 'actualizado' => 'Registro actualizado', 'eliminado' => 'Registro eliminado', 'firmado' => 'Firma recabada'];
        $entidades = ['contrato' => 'Contrato', 'firma' => 'Firmante', 'nota' => 'Nota'];
        return ($entidades[$this->entidad] ?? $this->entidad) . ' · ' . ($acciones[$this->accion] ?? $this->accion);
    }

    public function getDetalleCambios()
    {
        return json_decode($this->cambios, true, 512, JSON_THROW_ON_ERROR);
    }
}
