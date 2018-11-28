<?php

use yii\db\Migration;

class m170526_124336_add_vip_active_till extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `vip_active_till` timestamp NULL AFTER `status`;        
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD INDEX `vip_active_till` (`vip_active_till`);
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD INDEX `packet_vip_active_till` (`packet`, `vip_active_till`),
            DROP INDEX `vip_active_till`;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `next_vip_notification_at` timestamp NULL AFTER `vip_active_till`;
        ");
    }

    public function down()
    {
        echo "m170526_124336_add_vip_active_till cannot be reverted.\n";

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
