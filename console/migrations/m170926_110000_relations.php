<?php

use yii\db\Migration;

/**
 * Class m170926_110000_relations
 */
class m170926_110000_relations extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createIndex('fk_coordinates_user_idx', '{{%coordinates}}', 'id_user');
        $this->createIndex('relation_idx', 'relations', ['id_worker', 'id_courier'], true);
//
//        $this->createIndex('fk_coordinates_user_idx', '{{%coordinates}}', 'id_user');
//        $this->createIndex('fk_coordinates_user_idx', '{{%coordinates}}', 'id_user');

        $this->addForeignKey('fk_coordinate_user', '{{%coordinates}}', 'id_user', '{{%user}}', 'id');

        $this->addForeignKey('fk_relations_user_worker', '{{%relations}}', 'id_worker', '{{%user}}', 'id');
        $this->addForeignKey('fk_relations_user_courier', '{{%relations}}', 'id_courier', '{{%user}}', 'id');
    }


    public function down()
    {
        $this->dropIndex('relation_idx', 'relations');
        $this->dropIndex('relation_idx', 'relations');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170926_110000_relations cannot be reverted.\n";

        return false;
    }
    */
}
