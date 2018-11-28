<?php

use yii\db\Migration;

class m180316_075932_add_stat_referrals_vip_plus_for_user extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` ADD `stat_referrals_vip_plus` int(11) NOT NULL DEFAULT \'0\' AFTER `stat_referrals_vip`;');
    }

    public function down()
    {
        echo "m180316_075932_add_stat_referrals_vip_plus_for_user cannot be reverted.\n";

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
