<?php

use yii\db\Migration;

class m170213_101125_add_field_registration_from_desktop extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
                ADD `registration_from_desktop` tinyint(1) NOT NULL DEFAULT '0',
                ADD `show_start_popup` tinyint(1) NOT NULL DEFAULT '0' AFTER `registration_from_desktop`,
                ADD `show_friends_invite_popup` tinyint(1) NOT NULL DEFAULT '0' AFTER `show_start_popup`;
        ");
    }

    public function down()
    {
        echo "m170213_101125_add_field_registration_from_desktop cannot be reverted.\n";

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
