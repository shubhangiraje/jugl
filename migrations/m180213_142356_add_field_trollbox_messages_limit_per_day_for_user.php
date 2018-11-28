<?php

use yii\db\Migration;

class m180213_142356_add_field_trollbox_messages_limit_per_day_for_user extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` ADD `trollbox_messages_limit_per_day` int(11) NULL;');
    }

    public function down()
    {
        echo "m180213_142356_add_field_trollbox_messages_limit_per_day_for_user cannot be reverted.\n";

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
