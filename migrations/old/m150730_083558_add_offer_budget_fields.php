<?php

use yii\db\Schema;
use yii\db\Migration;

class m150730_083558_add_offer_budget_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `view_bonus_total` decimal(14,2) NULL AFTER `view_bonus`,
            ADD `view_bonus_used` decimal(14,2) NOT NULL DEFAULT '0' AFTER `view_bonus_total`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150730_083558_add_offer_budget_fields cannot be reverted.\n";

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
