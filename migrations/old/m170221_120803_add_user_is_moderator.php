<?php

use yii\db\Migration;

class m170221_120803_add_user_is_moderator extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `is_moderator` tinyint(1) NOT NULL DEFAULT '0';
        ");
    }

    public function down()
    {
        echo "m170221_120803_add_user_is_moderator cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
