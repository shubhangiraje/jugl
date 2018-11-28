<?php

use yii\db\Migration;

class m170217_105858_update_default_text_category extends Migration
{
    public function up()
    {
        $this->execute("UPDATE default_text SET category='OFFER_DELETE' WHERE category='OFFER'");
        $this->execute("UPDATE default_text SET category='SEARCH_REQUEST_DELETE' WHERE category='SEARCH_REQUEST'");

        $this->execute("ALTER TABLE `offer` DROP `reject_reason`;");
        $this->execute("ALTER TABLE `search_request` DROP `reject_reason`;");

    }

    public function down()
    {
        echo "m170217_105858_update_default_text_category cannot be reverted.\n";

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
