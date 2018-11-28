<?php

use yii\db\Migration;

class m180416_140958_add_new_field_for_search_request extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `search_request`
            CHANGE `status` `status` enum(\'ACTIVE\',\'EXPIRED\',\'CLOSED\',\'DELETED\',\'AWAITING_VALIDATION\',\'REJECTED\',\'UNLINKED\',\'SCHEDULED\') COLLATE \'utf8_general_ci\' NOT NULL DEFAULT \'ACTIVE\' AFTER `active_till`,
            ADD `scheduled_dt` timestamp NULL;
        ');

        $this->execute('ALTER TABLE `search_request` ADD INDEX `status` (`status`);');
    }

    public function down()
    {
        echo "m180416_140958_add_new_field_for_search_request cannot be reverted.\n";

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
