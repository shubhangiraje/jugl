<?php

use yii\db\Schema;
use yii\db\Migration;

class m151224_102745_add_offer_ad extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `type` enum('STANDARD','ADS') NOT NULL DEFAULT 'STANDARD' AFTER `create_dt`,
            ADD `allow_contact` tinyint(1) NOT NULL DEFAULT '0' AFTER `type`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151224_102745_add_offer_ad cannot be reverted.\n";

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
