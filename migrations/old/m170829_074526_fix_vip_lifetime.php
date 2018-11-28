<?php

use yii\db\Migration;

class m170829_074526_fix_vip_lifetime extends Migration
{
    public function up()
    {
        $this->execute("
          update `user` set packet='VIP',vip_lifetime=1, vip_active_till='2035-01-01 00:00:00' where `vip_lifetime` = '0' AND `vip_active_till` IS NOT NULL
        ");
    }

    public function down()
    {
        echo "m170829_074526_fix_vip_lifetime cannot be reverted.\n";

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
