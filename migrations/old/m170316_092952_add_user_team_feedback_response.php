<?php

use yii\db\Migration;

class m170316_092952_add_user_team_feedback_response extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_team_feedback`
            ADD `response` varchar(4096) NULL,
            ADD `response_dt` timestamp NULL AFTER `response`;
        ");
    }

    public function down()
    {
        echo "m170316_092952_add_user_team_feedback_response cannot be reverted.\n";

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
