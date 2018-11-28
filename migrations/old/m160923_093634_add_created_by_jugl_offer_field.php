<?php

use yii\db\Schema;
use yii\db\Migration;

class m160923_093634_add_created_by_jugl_offer_field extends Migration
{
    public function up()
    {
        $this->execute("
                ALTER TABLE `offer`
                ADD `created_by_jugl` tinyint(1) NULL DEFAULT '0';
        ");

        $this->execute("
                ALTER TABLE `offer`
                CHANGE `created_by_jugl` `created_by_admin` tinyint(1) NULL DEFAULT '0' AFTER `closed_dt`;
        ");
    }

    public function down()
    {
        echo "m160923_093634_add_created_by_jugl_offer_field cannot be reverted.\n";

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
