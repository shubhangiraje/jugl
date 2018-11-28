<?php

use yii\db\Schema;
use yii\db\Migration;

class m151228_101448_offer_ads_type_modifications extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            CHANGE `price` `price` decimal(14,2) NULL AFTER `description`,
            CHANGE `buy_bonus` `buy_bonus` decimal(14,2) NULL AFTER `view_bonus_used`,
            CHANGE `zip` `zip` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `buy_bonus`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151228_101448_offer_ads_type_modifications cannot be reverted.\n";

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
