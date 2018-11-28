<?php

use yii\db\Migration;

class m171214_101946_add_trollbox_message_sticky_flag extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `trollbox_message`
            ADD `is_sticky` tinyint(1) NULL DEFAULT '0';
        ");

        $this->execute("
            ALTER TABLE `trollbox_message`
            ADD INDEX `is_sticky_dt` (`is_sticky`, `dt`);
        ");
    }

    public function down()
    {
        echo "m171214_101946_add_trollbox_message_sticky_flag cannot be reverted.\n";

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
