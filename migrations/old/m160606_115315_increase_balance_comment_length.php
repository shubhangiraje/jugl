<?php

use yii\db\Schema;
use yii\db\Migration;

class m160606_115315_increase_balance_comment_length extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `balance_log`
            CHANGE `comment` `comment` varchar(512) COLLATE 'utf8_general_ci' NULL AFTER `initiator_user_id`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m160606_115315_increase_balance_comment_length cannot be reverted.\n";

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
