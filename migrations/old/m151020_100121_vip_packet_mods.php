<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_100121_vip_packet_mods extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE `setting` SET
            `name` = 'VIP_COST_CURRENCY',
            `title` = 'VIP cost in €',
            `type` = 2,
            `value` = '3'
            WHERE `name` = 'REGISTRATION_COST_CURRENCY' AND `name` = 'REGISTRATION_COST_CURRENCY' COLLATE utf8_bin;
        ");

        $this->execute("
            UPDATE `setting` SET
            `name` = 'VIP_COST_JUGL',
            `title` = 'VIP Währungsverhältnis in Jugl',
            `type` = 2,
            `value` = '300'
            WHERE `name` = 'REGISTRATION_COST_JUGL' AND `name` = 'REGISTRATION_COST_JUGL' COLLATE utf8_bin;
        ");
    }

    public function down()
    {
        echo "m151020_100121_vip_packet_mods cannot be reverted.\n";

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
