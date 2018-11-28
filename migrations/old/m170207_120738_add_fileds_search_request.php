<?php

use yii\db\Migration;

class m170207_120738_add_fileds_search_request extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
                CHANGE `status` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED','REJECTED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `active_till`,
                ADD `need_validation` tinyint(1) NOT NULL DEFAULT '0';
        ");
    }

    public function down()
    {
        echo "m170207_120738_add_fileds_search_request cannot be reverted.\n";

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
