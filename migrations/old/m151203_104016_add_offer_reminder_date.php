<?php

use yii\db\Schema;
use yii\db\Migration;

class m151203_104016_add_offer_reminder_date extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `pay_remindered_dt` timestamp NULL DEFAULT '0000-00-00 00:00:00' AFTER `pay_method`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151203_104016_add_offer_reminder_date cannot be reverted.\n";

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
