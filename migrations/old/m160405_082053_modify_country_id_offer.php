<?php

use yii\db\Schema;
use yii\db\Migration;

class m160405_082053_modify_country_id_offer extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
                CHANGE `country_id` `country_id` int(11) NULL AFTER `buy_bonus`;
        ");
    }

    public function down()
    {
        echo "m160405_082053_modify_country_id_offer cannot be reverted.\n";

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
