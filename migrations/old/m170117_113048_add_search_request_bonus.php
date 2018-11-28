<?php

use yii\db\Migration;

class m170117_113048_add_search_request_bonus extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `interest`
                CHANGE `view_bonus` `offer_view_bonus` int(11) NULL AFTER `sort_order`,
                ADD `search_request_bonus` int(11) NULL;
        ");
    }

    public function down()
    {
        echo "m170117_113048_add_search_request_bonus cannot be reverted.\n";

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
