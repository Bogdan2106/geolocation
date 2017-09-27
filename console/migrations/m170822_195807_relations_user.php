<?php

use yii\db\Migration;

/**
 * Class m170822_195807_relations_user
 */
class m170822_195807_relations_user extends Migration
{



    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
       /* $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%relations}}', [
            'id_worker' => $this->integer(),
            'id_courier' => $this->integer(),
        ], $tableOptions);*/

        $this->createIndex('relation_idx', '{{%relations}}', ['id_worker', 'id_courier'], true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('relation_idx', '{{%relations}}');
        $this->dropTable('{{%relations}}');
    }

}
