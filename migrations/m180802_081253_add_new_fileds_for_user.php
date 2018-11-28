<?php

use yii\db\Migration;

class m180802_081253_add_new_fileds_for_user extends Migration
{
    public function up()
    {

        $this->execute('
            ALTER TABLE `user`
            ADD `video_identification_status` enum(\'NONE\',\'AWAITING\',\'ACCEPTED\',\'REJECTED\') NOT NULL DEFAULT \'ACCEPTED\',
            ADD `video_identification_score` int(11) NOT NULL DEFAULT \'0\';
        ');

        $this->execute('ALTER TABLE `user` CHANGE `video_identification_status` `video_identification_status` enum(\'NONE\',\'AWAITING\',\'ACCEPTED\',\'REJECTED\') COLLATE \'utf8mb4_unicode_ci\' NOT NULL DEFAULT \'NONE\' AFTER `is_update_country_after_login`;');
    }

    public function down()
    {
        echo "m180802_081253_add_new_fileds_for_user cannot be reverted.\n";

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
