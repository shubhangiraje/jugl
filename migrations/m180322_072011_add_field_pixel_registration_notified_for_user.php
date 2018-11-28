<?php

use yii\db\Migration;

class m180322_072011_add_field_pixel_registration_notified_for_user extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` ADD `pixel_registration_notified` tinyint(1) NOT NULL DEFAULT \'1\';');
        $this->execute('ALTER TABLE `user` CHANGE `pixel_registration_notified` `pixel_registration_notified` tinyint(1) NOT NULL DEFAULT \'0\' AFTER `trollbox_messages_limit_per_day`;');
    }

    public function down()
    {
        echo "m180322_072011_add_field_pixel_registration_notified_for_user cannot be reverted.\n";

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
