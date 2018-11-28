<?php

use yii\db\Migration;

class m170131_140216_change_offer_bonus_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            CHANGE `view_bonus` `view_bonus` decimal(14,2) NULL AFTER `delivery_days`,
            CHANGE `view_bonus_total` `view_bonus_total` decimal(14,2) NULL AFTER `view_bonus`;        
        ");
    }

    public function down()
    {
        echo "m170131_140216_change_offer_bonus_fields cannot be reverted.\n";

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
