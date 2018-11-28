<?php

use yii\db\Migration;

class m171110_103254_add_visibility_to_trollbox_messages extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `trollbox_message`
            ADD `visible_for_all` tinyint NULL DEFAULT '0',
            ADD `visible_for_followers` tinyint NULL DEFAULT '0' AFTER `visible_for_all`,
            ADD `visible_for_contacts` tinyint NULL DEFAULT '0' AFTER `visible_for_followers`;
        ");

        $this->execute("
            update trollbox_message set visible_for_all=1
        ");
    }

    public function down()
    {
        echo "m171110_103254_add_visibility_to_trollbox_messages cannot be reverted.\n";

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
