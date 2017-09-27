<?php

use yii\db\Migration;

/**
 * Class m170925_105706_chat_user
 */
class m170925_105706_chat_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%chat_user}}', [
            'id' => $this->primaryKey()->unique(),
            'chat_id' => $this->integer()->notNull()->unique(),
            'user_id' => $this->string()->notNull(),
            'status' => $this->integer(),

            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m170925_105706_chat_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170925_105706_chat_user cannot be reverted.\n";

        return false;
    }
    */
}
