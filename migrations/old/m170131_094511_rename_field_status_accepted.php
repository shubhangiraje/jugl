<?php

use yii\db\Migration;

class m170131_094511_rename_field_status_accepted extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `pay_out_request` CHANGE `dt_accepted` `dt_processed` timestamp NULL AFTER `details`;");
    }

    public function down()
    {
        echo "m170131_094511_rename_field_status_accepted cannot be reverted.\n";

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
