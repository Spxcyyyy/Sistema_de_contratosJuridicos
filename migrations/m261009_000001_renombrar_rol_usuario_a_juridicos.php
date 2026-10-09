<?php

use yii\db\Migration;

class m261009_000001_renombrar_rol_usuario_a_juridicos extends Migration
{
    public function up()
    {
        $this->alterColumn(
            '{{%user}}',
            'role',
            "ENUM('admin','usuario','juridicos','recabador') NOT NULL DEFAULT 'juridicos'"
        );
        $this->update('{{%user}}', ['role' => 'juridicos'], ['role' => 'usuario']);
        $this->alterColumn(
            '{{%user}}',
            'role',
            "ENUM('admin','juridicos','recabador') NOT NULL DEFAULT 'juridicos'"
        );
    }

    public function down()
    {
        $this->alterColumn(
            '{{%user}}',
            'role',
            "ENUM('admin','juridicos','usuario','recabador') NOT NULL DEFAULT 'usuario'"
        );
        $this->update('{{%user}}', ['role' => 'usuario'], ['role' => 'juridicos']);
        $this->alterColumn(
            '{{%user}}',
            'role',
            "ENUM('admin','usuario','recabador') NOT NULL DEFAULT 'usuario'"
        );
    }
}
