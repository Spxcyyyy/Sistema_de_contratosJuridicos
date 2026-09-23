<?php

use yii\db\Migration;

class m260807_193504_add_ubicacion_archivo_to_contratos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contratos', 'ubicacion_archivo', $this->string(255)->null());
    }

    public function safeDown()
    {
        $this->dropColumn('contratos', 'ubicacion_archivo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260807_193504_add_ubicacion_archivo_to_contratos cannot be reverted.\n";

        return false;
    }
    */
}
