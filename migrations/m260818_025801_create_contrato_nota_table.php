<?php

use yii\db\Migration;

class m260818_025801_create_contrato_nota_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contrato_nota}}', [
            'id' => $this->primaryKey(),
            'contrato_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'contenido' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-contrato_nota-contrato_id',
            '{{%contrato_nota}}',
            'contrato_id',
            '{{%contratos}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-contrato_nota-user_id',
            '{{%contrato_nota}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-contrato_nota-contrato_id', '{{%contrato_nota}}');
        $this->dropForeignKey('fk-contrato_nota-user_id', '{{%contrato_nota}}');
        $this->dropTable('{{%contrato_nota}}');
    }
}