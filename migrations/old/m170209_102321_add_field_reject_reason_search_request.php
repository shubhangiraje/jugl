<?php

use yii\db\Migration;

class m170209_102321_add_field_reject_reason_search_request extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `search_request` ADD `reject_reason` mediumtext COLLATE 'utf8_general_ci' NULL;");
    }

    public function down()
    {
        echo "m170209_102321_add_field_reject_reason_search_request cannot be reverted.\n";

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
