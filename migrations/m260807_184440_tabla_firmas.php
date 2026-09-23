<?php

use yii\db\Migration;

class m260807_184440_tabla_firmas extends Migration
{
   public function safeUp()
    {
        $this->createTable('firmas', [
            'id' => $this->primaryKey(),
            'contrato_id' => $this->integer()->notNull(),
            'nombre' => $this->string(150)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-firmas-contrato_id',
            'firmas',
            'contrato_id',
            'contratos',
            'id',
            'CASCADE'
        );

        $this->createIndex('idx-firmas-contrato_id', 'firmas', 'contrato_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-firmas-contrato_id', 'firmas');
        $this->dropTable('firmas');
    }
}
