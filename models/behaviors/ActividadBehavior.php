<?php

namespace app\models\behaviors;

use Yii;
use app\models\Contrato;
use app\models\ContratoActividad;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/** Audit entries are saved in the same transaction as their owning record. */
class ActividadBehavior extends Behavior
{
    public $entidad;
    public $campos = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'registrar',
            ActiveRecord::EVENT_AFTER_UPDATE => 'registrar',
            ActiveRecord::EVENT_BEFORE_DELETE => 'registrar',
        ];
    }

    public function registrar($event)
    {
        $model = $this->owner;
        $crear = $event->name === ActiveRecord::EVENT_AFTER_INSERT;
        $eliminar = $event->name === ActiveRecord::EVENT_BEFORE_DELETE;
        $cambios = [];
        foreach ($this->campos as $campo) {
            if (!$crear && !$eliminar && !array_key_exists($campo, $event->changedAttributes)) {
                continue;
            }
            $antes = $crear ? null : ($eliminar ? $model->$campo : $event->changedAttributes[$campo]);
            $despues = $eliminar ? null : $model->$campo;
            if (!$crear && !$eliminar && (string) $antes === (string) $despues) {
                continue;
            }
            $cambios[$campo] = ['etiqueta' => $model->getAttributeLabel($campo), 'antes' => $antes, 'despues' => $despues];
        }
        if (!$crear && !$eliminar && !$cambios) {
            return;
        }
        $contrato = $model instanceof Contrato ? $model : Contrato::findOne($model->contrato_id);
        if (!$contrato) {
            throw new \RuntimeException('No se encontró el contrato para registrar su actividad.');
        }
        $identity = Yii::$app->has('user') ? Yii::$app->user->identity : null;
        $actividad = new ContratoActividad();
        $actividad->contrato_id = $contrato->id;
        $actividad->codigo = $contrato->codigo;
        $actividad->actor_id = $identity?->id;
        $actividad->actor_nombre = $identity?->username ?? 'Sistema';
        $actividad->entidad = $this->entidad;
        $actividad->entidad_id = $model->id;
        $actividad->accion = $crear ? 'creado' : ($eliminar ? 'eliminado' : 'actualizado');
        if (!$eliminar && $this->entidad === 'firma' && isset($cambios['estado']) && $model->estado === 'firmado') {
            $actividad->accion = 'firmado';
        }
        $actividad->cambios = json_encode($cambios, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $actividad->created_at = time();
        if (!$actividad->save()) {
            throw new \RuntimeException('No se pudo registrar la actividad.');
        }
    }
}
