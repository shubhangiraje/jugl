<?php

use yii\db\Migration;

class m171122_103238_add_new_payin_request_type extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_in_request`
            CHANGE `type` `type` enum('PAY_IN','PACKET','PACKET_VIP_PLUS') COLLATE 'utf8_general_ci' NOT NULL AFTER `confirm_status`;
        ");
    }

    public function down()
    {
        echo "m171122_103238_add_new_payin_request_type cannot be reverted.\n";

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
