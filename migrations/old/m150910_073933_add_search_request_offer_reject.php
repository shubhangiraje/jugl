<?php

use yii\db\Schema;
use yii\db\Migration;

class m150910_073933_add_search_request_offer_reject extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request_offer`
                ADD `reject_reason` enum('OFFER_NOT_FIT','CHANGED_MY_MIND','OFFER_IS_EXPENSIVE','OTHERS') COLLATE 'utf8_general_ci' NULL,
                ADD `reject_comment` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `reject_reason`;
        ");
    }

    public function down()
    {
        echo "m150910_073933_add_search_request_offer_reject cannot be reverted.\n";

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
