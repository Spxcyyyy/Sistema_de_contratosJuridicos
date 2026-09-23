<?php

use yii\db\Migration;

class m260923_120000_agregar_estado_eliminacion_logica extends Migration
{
    private const TABLES = ['contratos', 'firmas', 'user', 'contrato_nota', 'password_reset_request', 'contrato_actividad'];

    public function safeUp()
    {
        foreach (self::TABLES as $table) {
            $this->addColumn('{{%' . $table . '}}', 'status_registro', $this->string(10)->notNull()->defaultValue('activo'));
            $this->createIndex('idx-' . $table . '-status-registro', '{{%' . $table . '}}', 'status_registro');
        }
    }

    public function safeDown()
    {
        foreach (self::TABLES as $table) {
            if ((new \yii\db\Query())->from('{{%' . $table . '}}')->where(['status_registro' => 'eliminado'])->exists($this->db)) {
                echo "No se puede revertir: hay registros eliminados que volverían a ser visibles.\n";
                return false;
            }
        }
        foreach (array_reverse(self::TABLES) as $table) {
            $this->dropIndex('idx-' . $table . '-status-registro', '{{%' . $table . '}}');
            $this->dropColumn('{{%' . $table . '}}', 'status_registro');
        }
    }
}
