<?php

use yii\db\Migration;

class m260910_120000_seguimiento_contratos extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contratos', 'fecha_vencimiento', $this->date()->null());
        $this->createIndex('idx-contratos-vencimiento', 'contratos', ['fecha_vencimiento', 'estado']);
        $this->createTable('contrato_actividad', [
            'id' => $this->primaryKey(),
            'contrato_id' => $this->integer()->null(),
            'codigo' => $this->string(255)->notNull(),
            'actor_id' => $this->integer()->null(),
            'actor_nombre' => $this->string(255)->notNull(),
            'entidad' => $this->string(30)->notNull(),
            'entidad_id' => $this->integer()->notNull(),
            'accion' => $this->string(30)->notNull(),
            'cambios' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->createIndex('idx-actividad-contrato-fecha', 'contrato_actividad', ['contrato_id', 'created_at', 'id']);
        $this->createIndex('idx-actividad-fecha', 'contrato_actividad', ['created_at', 'id']);
        $this->addForeignKey('fk-actividad-contrato', 'contrato_actividad', 'contrato_id', 'contratos', 'id', 'SET NULL');
        $this->addForeignKey('fk-actividad-actor', 'contrato_actividad', 'actor_id', 'user', 'id', 'SET NULL');
    }

    public function safeDown()
    {
        $this->dropTable('contrato_actividad');
        $this->dropIndex('idx-contratos-vencimiento', 'contratos');
        $this->dropColumn('contratos', 'fecha_vencimiento');
    }
}
