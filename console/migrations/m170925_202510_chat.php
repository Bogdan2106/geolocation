<?php

use yii\db\Migration;

/**
 * Class m170925_202510_chat
 */
class m170925_202510_chat extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('chat', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'status' => $this->smallInteger()->defaultValue(10),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m170925_202510_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170925_202510_chat cannot be reverted.\n";

        return false;
    }
    */
}
