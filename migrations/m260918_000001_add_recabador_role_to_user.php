<?php

use yii\db\Migration;

class m260918_000001_add_recabador_role_to_user extends Migration
{
    public function up()
    {
        $this->alterColumn(
            '{{%user}}',
            'role',
            "ENUM('admin','usuario','recabador') NOT NULL DEFAULT 'usuario'"
        );
    }

    public function down()
    {
        if ((new \yii\db\Query())->from('{{%user}}')->where(['role' => 'recabador'])->exists($this->db)) {
            echo "No se puede revertir: existen usuarios con el rol recabador.\n";
            return false;
        }

        $this->alterColumn(
            '{{%user}}',
            'role',
            "ENUM('admin','usuario') NOT NULL DEFAULT 'usuario'"
        );
    }
}
