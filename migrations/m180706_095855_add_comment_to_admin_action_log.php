<?php

use yii\db\Migration;

class m180706_095855_add_comment_to_admin_action_log extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `admin_action_log`
            CHANGE `dt` `dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP AFTER `id`,
            ADD `comment` mediumtext NULL;
        ");
    }

    public function down()
    {
        echo "m180706_095855_add_comment_to_admin_action_log cannot be reverted.\n";

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
