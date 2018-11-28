<?php

use yii\db\Migration;

class m170310_111956_add_user_block_parent_team_requests extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `block_parent_team_requests` tinyint(1) NOT NULL DEFAULT '0';
        ");
    }

    public function down()
    {
        echo "m170310_111956_add_user_block_parent_team_requests cannot be reverted.\n";

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
