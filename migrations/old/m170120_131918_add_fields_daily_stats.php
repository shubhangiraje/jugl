<?php

use yii\db\Migration;

class m170120_131918_add_fields_daily_stats extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `daily_stats`
                ADD `packet_select_vip` int(11) NOT NULL DEFAULT '0',
                ADD `packet_select_standard` int(11) NOT NULL DEFAULT '0' AFTER `packet_select_vip`;
        ");
    }

    public function down()
    {
        echo "m170120_131918_add_fields_daily_stats cannot be reverted.\n";

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
