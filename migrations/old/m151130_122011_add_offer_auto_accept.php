<?php

use yii\db\Schema;
use yii\db\Migration;

class m151130_122011_add_offer_auto_accept extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `auto_accept` tinyint(1) NOT NULL DEFAULT '0' AFTER `status`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151130_122011_add_offer_auto_accept cannot be reverted.\n";

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
