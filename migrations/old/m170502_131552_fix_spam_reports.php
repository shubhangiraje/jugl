<?php

use yii\db\Migration;

class m170502_131552_fix_spam_reports extends Migration
{
    public function up()
    {
        $this->execute("
          update user set spam_reports=(select count(*) from user_spam_report usr where usr.second_user_id=user.id and is_active=1) 
        ");
    }

    public function down()
    {
        echo "m170502_131552_fix_spam_reports cannot be reverted.\n";

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
