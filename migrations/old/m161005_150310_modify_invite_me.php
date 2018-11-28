<?php

use yii\db\Schema;
use yii\db\Migration;

class m161005_150310_modify_invite_me extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `invite_me`
                ADD `invited_count` int(11) NOT NULL DEFAULT '0',
                ADD `invited_by_sms` tinyint(1) NOT NULL DEFAULT '0' AFTER `invited_count`,
                ADD `invited_by_email` tinyint(1) NOT NULL DEFAULT '0' AFTER `invited_by_sms`;
        ");
    }

    public function down()
    {
        echo "m161005_150310_modify_invite_me cannot be reverted.\n";

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
