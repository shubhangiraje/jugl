<?php

use yii\db\Migration;

class m170120_125239_add_fields_dt_upgrade_and_select_packet_user extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
                ADD `dt_packet_upgrade` timestamp NULL,
                ADD `dt_packet_select` timestamp NULL AFTER `dt_packet_upgrade`;
        ");
    }

    public function down()
    {
        echo "m170120_125239_add_fields_dt_upgrade_and_select_packet_user cannot be reverted.\n";

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
