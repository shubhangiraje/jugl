<?php

use yii\db\Migration;

class m180319_091844_add_device_uuid_for_trollbox_message extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `trollbox_message` ADD `device_uuid` varchar(128) NULL;');
    }

    public function down()
    {
        echo "m180319_091844_add_device_uuid_for_trollbox_message cannot be reverted.\n";

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
