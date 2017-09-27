<?php

use yii\db\Migration;

/**
 * Class m170822_121013_coordinates
 */
class m170822_121013_coordinates extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%coordinates}}', [
            'id_user' => $this->integer(),
            'lng' => $this->decimal(10,7),
            'lat' => $this->decimal(10,7),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk_code', 'coordinates', 'id_user');
    }

    public function down()
    {
        $this->dropTable('{{%coordinates}}');
    }
}
