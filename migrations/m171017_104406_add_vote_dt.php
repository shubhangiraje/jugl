<?php

use yii\db\Migration;

class m171017_104406_add_vote_dt extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `info_comment_vote`
            ADD `dt` timestamp NULL;
        ");

        $this->execute("
            ALTER TABLE `info_comment_vote`
            ADD INDEX `dt` (`dt`);
        ");

        $this->execute("
            ALTER TABLE `trollbox_message_vote`
            ADD `dt` timestamp NULL;
        ");

        $this->execute("
            ALTER TABLE `trollbox_message_vote`
            ADD INDEX `dt` (`dt`);
        ");
    }

    public function down()
    {
        echo "m171017_104406_add_vote_dt cannot be reverted.\n";

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
