<?php

use yii\db\Migration;

class m260818_013834_create_password_reset_request_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%password_reset_request}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'email' => $this->string()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0), // 0 = pendiente, 1 = resuelto
            'created_at' => $this->integer()->notNull(),
            'resolved_at' => $this->integer()->null(),
            'resolved_by' => $this->integer()->null(),
        ]);

        $this->addForeignKey(
            'fk-password_reset_request-user_id',
            '{{%password_reset_request}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-password_reset_request-user_id', '{{%password_reset_request}}');
        $this->dropTable('{{%password_reset_request}}');
    }
}