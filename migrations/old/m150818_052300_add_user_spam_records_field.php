<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_052300_add_user_spam_records_field extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `spam_reports` int NOT NULL DEFAULT '0',
            COMMENT='';

            update user set spam_reports=(select count(*) from user_spam_report where second_user_id=user.id);
        ");
    }

    public function down()
    {
        echo "m150818_052300_add_user_spam_records_field cannot be reverted.\n";

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
