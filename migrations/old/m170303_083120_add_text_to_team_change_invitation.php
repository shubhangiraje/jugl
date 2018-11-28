<?php

use yii\db\Migration;

class m170303_083120_add_text_to_team_change_invitation extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_team_invitation`
            ADD `text` mediumtext NOT NULL;
        ");
    }

    public function down()
    {
        echo "m170303_083120_add_text_to_team_change_invitation cannot be reverted.\n";

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
