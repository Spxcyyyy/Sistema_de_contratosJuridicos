<?php

use yii\db\Migration;

class m260807_203131_agregar_estado_firmas extends Migration
{
    public function safeUp()
    {
        $this->addColumn('firmas', 'estado', $this->string(20)->notNull()->defaultValue('pendiente'));
        $this->addColumn('firmas', 'fecha_firma', $this->integer()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('firmas', 'fecha_firma');
        $this->dropColumn('firmas', 'estado');
    }
}
