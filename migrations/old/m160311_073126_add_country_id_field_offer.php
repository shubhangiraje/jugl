<?php

use yii\db\Schema;
use yii\db\Migration;

class m160311_073126_add_country_id_field_offer extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
                ADD `country_id` int(11) NULL AFTER `buy_bonus`,
                    ADD FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);
        ");

        $this->execute("
            UPDATE `offer` SET country_id = 64;
        ");

        $this->execute("
            ALTER TABLE `offer`
                CHANGE `country_id` `country_id` int(11) NOT NULL AFTER `buy_bonus`;
        ");

    }

    public function down()
    {
        echo "m160311_073126_add_country_id_field_offer cannot be reverted.\n";

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
