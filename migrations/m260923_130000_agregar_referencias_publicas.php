<?php

use yii\db\Migration;

class m260923_130000_agregar_referencias_publicas extends Migration
{
    public function safeUp()
    {
        foreach (['user', 'firmas'] as $table) {
            $name = '{{%' . $table . '}}';
            $this->addColumn($name, 'referencia_publica', $this->string(24)->null());
            $this->createIndex('idx-' . $table . '-referencia-publica', $name, 'referencia_publica', true);
            foreach ((new \yii\db\Query())->select('id')->from($name)->each(100, $this->db) as $row) {
                $this->update($name, ['referencia_publica' => bin2hex(random_bytes(12))], ['id' => $row['id']]);
            }
            $this->alterColumn($name, 'referencia_publica', $this->string(24)->notNull());
        }
    }

    public function safeDown()
    {
        foreach (['firmas', 'user'] as $table) {
            $name = '{{%' . $table . '}}';
            $this->dropIndex('idx-' . $table . '-referencia-publica', $name);
            $this->dropColumn($name, 'referencia_publica');
        }
    }
}
