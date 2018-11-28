<?php

use yii\db\Migration;

class m180108_130321_add_vip_plus_to_uf_packet_for_offer extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `offer` CHANGE `uf_packet` `uf_packet` enum('ALL','STANDART','VIP','VIP_PLUS') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ALL' AFTER `uf_sex`;");
    }

    public function down()
    {
        echo "m180108_130321_add_vip_plus_to_uf_packet_for_offer cannot be reverted.\n";

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
