<?php

use yii\db\Schema;
use yii\db\Migration;

class m160310_135156_add_country_id_field_search_request extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
                ADD `country_id` int(11) NULL AFTER `bonus`,
                    ADD FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);
        ");

        $this->execute("
            UPDATE `search_request` SET country_id = 64;
        ");

        $this->execute("
            ALTER TABLE `search_request`
                CHANGE `country_id` `country_id` int(11) NOT NULL AFTER `bonus`;
        ");

    }

    public function down()
    {
        echo "m160310_135156_add_country_id_field_search_request cannot be reverted.\n";

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
