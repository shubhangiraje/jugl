<?php

use yii\db\Migration;

class m170303_123616_mod_user_team_invitation extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user_team_invitation`
RENAME TO `user_team_request`;
        ");

        $this->execute("
ALTER TABLE `user_team_request`
ADD `type` enum('PARENT_TO_REFERRAL','REFERRAL_TO_PARENT') COLLATE 'utf8_general_ci' NOT NULL;
        ");
    }

    public function down()
    {
        echo "m170303_123616_mod_user_team_invitation cannot be reverted.\n";

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
