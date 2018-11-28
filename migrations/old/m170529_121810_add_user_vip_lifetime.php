<?php

use yii\db\Migration;

class m170529_121810_add_user_vip_lifetime extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `vip_lifetime` tinyint(1) NULL DEFAULT '0' AFTER `vip_active_till`;
        ");

        $this->execute("update user set vip_lifetime=1 where packet='VIP'");
    }

    public function down()
    {
        echo "m170529_121810_add_user_vip_lifetime cannot be reverted.\n";

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
