<?php

use yii\db\Migration;

class m180418_075826_add_new_field_for_offer extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `offer`
            CHANGE `status` `status` enum(\'ACTIVE\',\'EXPIRED\',\'CLOSED\',\'DELETED\',\'PAUSED\',\'AWAITING_VALIDATION\',\'REJECTED\',\'UNLINKED\',\'SCHEDULED\') COLLATE \'utf8_general_ci\' NOT NULL DEFAULT \'ACTIVE\' AFTER `accepted_offer_request_id`,
            ADD `scheduled_dt` timestamp NULL;
        ');
        $this->execute('ALTER TABLE `offer` ADD INDEX `status` (`status`);');
    }

    public function down()
    {
        echo "m180418_075826_add_new_field_for_offer cannot be reverted.\n";

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
