<?php

use yii\db\Migration;

class m180802_080650_add_type_for_trollbox_message extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `trollbox_message` ADD `type` enum(\'FORUM\',\'VIDEO_IDENTIFICATION\') COLLATE \'utf8mb4_unicode_ci\' NOT NULL DEFAULT \'FORUM\';');
        $this->execute('ALTER TABLE `trollbox_message` CHANGE `text` `text` mediumtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `dt`;');
    }

    public function down()
    {
        echo "m180802_080650_add_type_for_trollbox_message cannot be reverted.\n";

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
