<?php

use yii\db\Migration;

class m260807_184317_tabla_contratos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('contratos', [
            'id' => $this->primaryKey(),
            'codigo' => $this->string()->notNull()->unique(),
            'encargado' => $this->string(150)->notNull(),
            'nomenclatura' => $this->string(150)->notNull(),
            'fecha_documento' => $this->date()->notNull(),
            'estado' => $this->string()->notNull()->defaultValue('En proceso de firmas'),
            'costo' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260807_184317_tabla_contratos cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260807_184317_tabla_contratos cannot be reverted.\n";

        return false;
    }
    */
}
