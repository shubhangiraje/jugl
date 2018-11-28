<?php

use yii\db\Schema;
use yii\db\Migration;

class m160523_122738_add_search_request_offer_create_dt extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request_offer`
            ADD `create_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `search_request_id`,
            COMMENT='';
        ");

        $this->execute("
            update search_request_offer sro
            join search_request sr on (sr.id=sro.search_request_id)
            set sro.create_dt=sr.create_dt
        ");
    }

    public function down()
    {
        echo "m160523_122738_add_search_request_offer_create_dt cannot be reverted.\n";

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
