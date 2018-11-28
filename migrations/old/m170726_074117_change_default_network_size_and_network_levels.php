<?php

use yii\db\Migration;

class m170726_074117_change_default_network_size_and_network_levels extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user`
CHANGE `network_size` `network_size` int(11) NOT NULL DEFAULT '1' AFTER `paypal_email`,
CHANGE `network_levels` `network_levels` int(11) NOT NULL DEFAULT '1' AFTER `invitations`;
        ");
    }

    public function down()
    {
        echo "m170726_074117_change_default_network_size_and_network_levels cannot be reverted.\n";

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
