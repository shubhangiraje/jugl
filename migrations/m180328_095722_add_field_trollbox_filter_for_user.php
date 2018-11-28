<?php

use yii\db\Migration;

class m180328_095722_add_field_trollbox_filter_for_user extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` ADD `trollbox_filter` text NULL;');
    }

    public function down()
    {
        echo "m180328_095722_add_field_trollbox_filter_for_user cannot be reverted.\n";

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
