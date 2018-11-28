<?php

use yii\db\Schema;
use yii\db\Migration;

class m150924_094526_make_bonus_fields_required extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            CHANGE `view_bonus` `view_bonus` decimal(14,2) NOT NULL AFTER `delivery_days`,
            CHANGE `view_bonus_total` `view_bonus_total` decimal(14,2) NOT NULL AFTER `view_bonus`,
            CHANGE `buy_bonus` `buy_bonus` decimal(14,2) NOT NULL AFTER `view_bonus_used`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150924_094526_make_bonus_fields_required cannot be reverted.\n";

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
