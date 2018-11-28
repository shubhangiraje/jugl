<?php

use yii\db\Migration;

class m171121_110608_add_vip_plus_packet extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `packet` `packet` enum('','STANDART','VIP','VIP_PLUS') COLLATE 'utf8_general_ci' NULL AFTER `deleted_last_name`;
        ");
    }

    public function down()
    {
        echo "m171121_110608_add_vip_plus_packet cannot be reverted.\n";

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
